<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateAdminController extends Controller
{
    protected function normalizeRole(?string $role, string $default = ''): string
    {
        $role = strtolower(trim((string) $role));
        if ($role === 'admin') return 'superuser';
        if ($role === 'super_user') return 'superuser';
        if ($role === 'kemasis') return 'kemahasiswaan';
        return $role ?: $default;
    }

    protected function authorizeEventByRole(Event $event): void
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role, 'superuser');

        if ($userRole === 'superuser') return;

        $eventRole = $this->normalizeRole($event->owner_role, '');

        if (empty($eventRole)) {
            if ((int) $event->created_by_user_id !== (int) $user->id) {
                abort(403, 'Anda tidak memiliki izin untuk mengelola sertifikat event ini.');
            }
            return;
        }

        if ($eventRole !== $userRole) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola sertifikat event ini.');
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role, 'superuser');

        $events = Event::with(['category'])
            ->withCount([
                'registrations as attended_count' => fn ($q) => $q->where('status', 'attended')
            ])
            ->when($userRole !== 'superuser', function ($q) use ($userRole, $user) {
                $q->where(function ($qq) use ($userRole, $user) {
                    $qq->where('owner_role', $userRole)
                        ->orWhere(function ($qx) use ($user) {
                            $qx->whereNull('owner_role')->where('created_by_user_id', $user->id);
                        });
                });
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where('nama_event', 'like', "%{$request->search}%");
            })
            ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                $q->whereBetween('tanggal_pelaksanaan', [$request->start_date, $request->end_date]);
            })
            ->when($request->month, function ($q) use ($request) {
                $q->whereMonth('tanggal_pelaksanaan', $request->month);
            })
            ->when($request->year, function ($q) use ($request) {
                $q->whereYear('tanggal_pelaksanaan', $request->year);
            })
            ->when($request->date_filter, function ($q) use ($request) {
                // Optional: specialized processing if single date input is used alongside range
            })
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->paginate($request->limit ?? 20);

        return view('admin.certificates.index', compact('events'));
    }

    public function eventDetail($event_id)
    {
        $event = Event::with(['category'])->findOrFail($event_id);
        $this->authorizeEventByRole($event);

        $registrations = EventRegistration::with('mahasiswa')
            ->where('event_id', $event_id)
            ->get();

        return view('admin.certificates.detail', [
            'event' => $event,
            'registrations' => $registrations,
        ]);
    }

    public function apiEventParticipants($event_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorizeEventByRole($event);

        $registrations = EventRegistration::with('mahasiswa')
            ->where('event_id', $event_id)
            ->where('status', 'attended')
            ->get()
            ->map(function ($reg) {
                $mhs = $reg->mahasiswa;

                return [
                    'registration_id' => $reg->id,
                    'user_id'         => $reg->user_id,
                    'nama'            => $mhs->nama_mahasiswa ?? 'Nama Peserta',
                    'npm'             => $mhs->npm_mahasiswa ?? '-',
                    'role'            => $reg->role ?? 'peserta',
                ];
            });

        return response()->json([
            'canvas_width'  => 3508,
            'canvas_height' => 2480,
            'participants'  => $registrations,
        ]);
    }

    public function uploadGenerated(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer',
            'user_id'  => 'required|integer',
            'file'     => 'required|file|mimes:jpg,png,jpeg,pdf|max:10240',
        ]);

        $event = Event::findOrFail($request->event_id);
        $this->authorizeEventByRole($event);

        // pastikan memang ada registrasi untuk event tsb + (optional) hanya yang attended
        $reg = EventRegistration::where('event_id', $request->event_id)
            ->where('user_id', $request->user_id)
            ->firstOrFail();

        $dir = "certificates/{$request->event_id}";
        Storage::disk('public')->makeDirectory($dir);

        $ext = $request->file('file')->getClientOriginalExtension();
        $ext = strtolower($ext);
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
            $ext = 'jpg';
        }
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $filename = "cert-{$request->event_id}-{$request->user_id}.{$ext}";
        $path = $request->file('file')->storeAs($dir, $filename, 'public');

        $reg->update([
            'certificate_url'         => "storage/{$path}",
            'certificate_uploaded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'url'     => asset("storage/" . $path),
        ]);
    }

    public function deleteCertificate($registration_id)
    {
        $reg = EventRegistration::findOrFail($registration_id);
        $event = Event::findOrFail($reg->event_id);
        $this->authorizeEventByRole($event);

        if ($reg->certificate_url) {
            $relative = str_replace('storage/', '', $reg->certificate_url);
            Storage::disk('public')->delete($relative);
        }

        $reg->update([
            'certificate_url'         => null,
            'certificate_uploaded_at' => null,
        ]);

        return back()->with('success', 'Sertifikat dihapus.');
    }
}

