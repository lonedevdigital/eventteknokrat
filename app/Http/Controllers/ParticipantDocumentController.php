<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventDocumentRequirement;
use App\Models\EventRegistration;
use App\Models\ParticipantDocument;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParticipantDocumentController extends Controller
{
    /**
     * Upload / ganti berkas mahasiswa untuk satu requirement lomba tertentu.
     */
    public function upload(Request $request, string $slug, EventDocumentRequirement $requirement): RedirectResponse
    {
        $event = Event::query()
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        if ((int) $requirement->event_id !== (int) $event->id) {
            abort(404);
        }

        // Cari registrasi milik user yang cabangnya cocok dengan cabang requirement ini — mahasiswa
        // bisa punya lebih dari satu registrasi (beda cabang) untuk lomba yang sama, jadi tidak
        // cukup ambil "yang pertama ketemu".
        $registrationQuery = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('user_id', $request->user()->id);
        $requirement->branch_id
            ? $registrationQuery->where('branch_id', $requirement->branch_id)
            : $registrationQuery->whereNull('branch_id');
        $registration = $registrationQuery->first();

        if (! $registration) {
            abort(403, 'Anda belum terdaftar pada cabang lomba ini.');
        }

        // Berkas lomba adalah kebutuhan tim, bukan per-anggota — hanya ketua tim yang boleh upload.
        if ($registration->tipe_pendaftaran === 'tim') {
            $isKetua = Team::where('event_id', $event->id)
                ->where('ketua_user_id', $request->user()->id)
                ->exists();

            if (! $isKetua) {
                abort(403, 'Hanya ketua tim yang dapat mengupload berkas.');
            }
        }

        $existing = ParticipantDocument::where('event_document_requirement_id', $requirement->id)
            ->where('event_registration_id', $registration->id)
            ->first();

        if ($existing && $existing->status === 'approved') {
            return back()->with('error', 'Berkas "' . $requirement->nama_dokumen . '" sudah disetujui dan tidak dapat diubah.');
        }

        if ($requirement->jenis_file === 'link') {
            $request->validate([
                'file_link' => 'required|url|max:255',
            ]);
            $filePath = $request->input('file_link');
        } else {
            $rules = $requirement->jenis_file === 'pdf'
                ? 'required|file|mimes:pdf|max:5120'
                : 'required|image|mimes:jpeg,png,jpg|max:2048';

            $request->validate(['file' => $rules]);

            if ($existing) {
                $this->deleteFile($existing->file_path);
            }

            $path = $request->file('file')->store("documents/{$event->id}/{$requirement->id}", 'public');
            $filePath = 'storage/' . $path;
        }

        ParticipantDocument::updateOrCreate(
            [
                'event_document_requirement_id' => $requirement->id,
                'event_registration_id' => $registration->id,
            ],
            [
                'file_path' => $filePath,
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
                'uploaded_at' => now(),
            ]
        );

        return redirect()
            ->route('frontend.my-event.show', $event->slug ?: $event->id)
            ->with('success', 'Berkas "' . $requirement->nama_dokumen . '" berhasil diupload dan menunggu review.');
    }

    protected function deleteFile(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, 'storage/')) {
            return;
        }

        $relative = Str::after($path, 'storage/');
        if ($relative !== '') {
            Storage::disk('public')->delete($relative);
        }
    }
}
