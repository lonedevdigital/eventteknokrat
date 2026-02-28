<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventAttendanceApiController extends Controller
{
    public function __construct()
    {
        // kalau kamu sudah pakai group middleware auth di routes, ini opsional
        $this->middleware('auth');
    }

    /**
     * Toggle attendance dari checkbox (AJAX)
     * Body: { present: true/false }
     */
    public function setAttendance(Request $request, EventRegistration $registration): JsonResponse
    {
        try {
            $validated = $request->validate([
                'present' => ['required', 'boolean'],
            ]);

            $present = (bool) $validated['present'];

            if ($present) {
                $registration->update([
                    'attendance_status' => 'present',
                    'attendance_at'     => now(),
                    'status'            => 'attended',
                ]);
            } else {
                $registration->update([
                    'attendance_status' => 'absent',
                    'attendance_at'     => null,
                    'status'            => 'registered',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $present ? 'Ditandai HADIR.' : 'Ditandai TIDAK HADIR.',
                'data' => [
                    'id' => $registration->id,
                    'attendance_status' => $registration->attendance_status,
                    'attendance_at' => $registration->attendance_at?->toDateTimeString(),
                    'status' => $registration->status,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->jsonError('Validasi gagal', 422, $e->errors());
        } catch (\Throwable $e) {
            return $this->jsonError('Terjadi error saat update kehadiran: '.$e->getMessage(), 500);
        }
    }

    /**
     * Tambah peserta manual berdasarkan NPM + Prodi
     * Body: { npm: "23312099", prodi: "informatika" / "55201" (opsional) }
     */
    public function addParticipant(Request $request, Event $event): JsonResponse
    {
        try {
            $validated = $request->validate([
                'npm'   => ['required', 'string', 'max:50'],
                'prodi' => ['nullable', 'string', 'max:255'],
            ]);

            $npm   = trim($validated['npm']);
            $prodi = isset($validated['prodi']) ? trim($validated['prodi']) : null;

            // Cari mahasiswa by NPM
            $q = Mahasiswa::query()->where('npm_mahasiswa', $npm);

            // Optional filter prodi
            if (!empty($prodi)) {
                if (ctype_digit($prodi)) {
                    $q->where('kode_program_studi', (int) $prodi);
                } else {
                    $q->where('nama_program_studi', 'like', '%'.$prodi.'%');
                }
            }

            $mhs = $q->first();

            if (!$mhs) {
                return $this->jsonError('Mahasiswa tidak ditemukan (cek NPM/Prodi).', 404);
            }

            // Penting: event_registrations pakai user_id
            if (!$mhs->user_id) {
                return $this->jsonError(
                    'Mahasiswa ditemukan, tapi belum terhubung ke akun user (mahasiswas.user_id kosong). Sinkronkan data mahasiswa dulu.',
                    422
                );
            }

            // Buat registrasi kalau belum ada
            $reg = EventRegistration::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'user_id'  => $mhs->user_id,
                ],
                [
                    'status'            => 'registered',
                    'attendance_status' => 'pending',
                    'attendance_at'     => null,
                    'registered_at'     => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => $reg->wasRecentlyCreated ? 'Peserta berhasil ditambahkan.' : 'Peserta sudah terdaftar.',
                'data' => [
                    'registration_id' => $reg->id,
                    'event_id'        => $reg->event_id,
                    'user_id'         => $reg->user_id,
                    'status'          => $reg->status,
                    'attendance_status' => $reg->attendance_status,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->jsonError('Validasi gagal', 422, $e->errors());
        } catch (\Throwable $e) {
            return $this->jsonError('Terjadi error saat menambah peserta: '.$e->getMessage(), 500);
        }
    }

    /**
     * Helper response error JSON yang konsisten
     */
    private function jsonError(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }
}
