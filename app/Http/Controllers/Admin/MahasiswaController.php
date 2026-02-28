<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ExternalApiController;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman list + filter mahasiswa.
     */
    public function index(Request $request)
    {
        $title = 'Data Mahasiswa';
        $dataProdi = $this->getProdiOptions();

        $angkatan = $request->get('angkatan');
        $prodiId = $request->get('prodi');

        if ($angkatan && $prodiId) {
            $dataMahasiswa = Mahasiswa::query()
                ->where('kode_program_studi', $prodiId)
                ->where('angkatan', $angkatan)
                ->orderBy('npm_mahasiswa')
                ->get();
        } else {
            $dataMahasiswa = collect();
        }

        return view('admin.mahasiswa.index', compact(
            'title',
            'dataMahasiswa',
            'dataProdi',
            'angkatan',
            'prodiId'
        ));
    }

    /**
     * Sinkronisasi data mahasiswa dari API eksternal ke database.
     */
    public function store(Request $request)
    {
        ini_set('max_execution_time', '5000');

        $validated = $request->validate([
            'angkatan' => ['required', 'digits:4'],
            'prodi' => ['nullable', 'string', 'max:20'],
        ]);

        $angkatan = (string) $validated['angkatan'];
        $prodiId = isset($validated['prodi']) && $validated['prodi'] !== ''
            ? (string) $validated['prodi']
            : null;

        $dataMahasiswa = ExternalApiController::getMahasiswa([
            'angkatan' => $angkatan,
        ]);

        if (empty($dataMahasiswa)) {
            return back()->with([
                'msg' => "Sinkronisasi gagal. API mahasiswa untuk angkatan {$angkatan} tidak mengembalikan data.",
                'class' => 'alert-danger',
            ]);
        }

        // Endpoint mahasiswa saat ini paling konsisten pada param angkatan.
        // Jika user memilih prodi, lakukan filter di backend agar hasil tetap sesuai.
        if ($prodiId) {
            $dataMahasiswa = array_values(array_filter($dataMahasiswa, function ($mhs) use ($prodiId) {
                return (string) ($mhs->id_prodi ?? '') === $prodiId;
            }));
        }

        if (empty($dataMahasiswa)) {
            $scope = $prodiId ? "angkatan {$angkatan} dan prodi {$prodiId}" : "angkatan {$angkatan}";

            return back()->with([
                'msg' => "Sinkronisasi selesai, tetapi tidak ada data untuk {$scope}.",
                'class' => 'alert-warning',
            ]);
        }

        $countSynced = 0;
        $countCreatedUser = 0;
        $countLinked = 0;

        foreach ($dataMahasiswa as $mhs) {
            if (empty($mhs->npm) || empty($mhs->nama)) {
                continue;
            }

            $npm = (string) $mhs->npm;
            $nama = (string) $mhs->nama;
            $angkatanValue = isset($mhs->angkatan) ? (string) $mhs->angkatan : $angkatan;

            $user = User::query()->where('username', $npm)->first();

            if (!$user) {
                $user = User::create([
                    'username' => $npm,
                    'email' => "{$npm}@teknokrat.ac.id",
                    'name' => $nama,
                    'role' => 'mahasiswa',
                    'password' => Hash::make($npm),
                ]);
                $countCreatedUser++;
            } else {
                $updateUser = [];
                $expectedEmail = "{$npm}@teknokrat.ac.id";

                if (empty($user->email) || $user->email !== $expectedEmail) {
                    $updateUser['email'] = $expectedEmail;
                }

                if (!empty($nama) && $user->name !== $nama) {
                    $updateUser['name'] = $nama;
                }

                if (empty($user->role)) {
                    $updateUser['role'] = 'mahasiswa';
                }

                if (!empty($updateUser)) {
                    $user->update($updateUser);
                }
            }

            $mhsRow = Mahasiswa::updateOrCreate(
                ['npm_mahasiswa' => $npm],
                [
                    'user_id' => $user->id,
                    'nama_mahasiswa' => $nama,
                    'kode_program_studi' => $mhs->id_prodi ?? null,
                    'nama_program_studi' => $mhs->nama_prodi ?? null,
                    'kode_fakultas' => $mhs->id_fakultas ?? null,
                    'nama_fakultas' => $mhs->nama_fakultas ?? null,
                    'nama_program_studi_english' => $mhs->nama_prodi_eng ?? null,
                    'nama_fakultas_english' => $mhs->nama_fakultas_eng ?? null,
                    'angkatan' => $angkatanValue,
                    'gender' => $mhs->gender ?? null,
                ]
            );

            if ($mhsRow && $mhsRow->user_id === $user->id) {
                $countLinked++;
            }

            $countSynced++;
        }

        $scope = $prodiId ? "angkatan {$angkatan} (prodi {$prodiId})" : "angkatan {$angkatan}";

        return back()->with([
            'msg' => "Sinkronisasi API berhasil untuk {$scope}. Diproses: {$countSynced}. User baru: {$countCreatedUser}. Link user_id: {$countLinked}.",
            'class' => 'alert-success',
        ]);
    }

    /**
     * Reset password user: password = username (npm).
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($user->username),
        ]);

        return back()->with([
            'msg' => 'Reset Password Berhasil',
            'class' => 'alert-success',
        ]);
    }

    /**
     * Ambil opsi prodi dari API (cached), fallback ke file lokal lama jika API gagal.
     */
    private function getProdiOptions(): array
    {
        $cacheKey = 'teknokrat.api.prodi';

        $dataProdi = Cache::remember($cacheKey, now()->addHours(6), function () {
            return ExternalApiController::getProdi();
        });

        if (!empty($dataProdi)) {
            return $dataProdi;
        }

        return $this->getProdiFromLocalFile();
    }

    /**
     * Fallback: baca prodi.json lama dari storage.
     */
    private function getProdiFromLocalFile(): array
    {
        $prodiFile = storage_path('app/teknokrat/prodi.json');

        if (!file_exists($prodiFile)) {
            return [];
        }

        $jsonContent = file_get_contents($prodiFile);
        $decoded = json_decode($jsonContent);

        $data = $decoded->data ?? [];
        return is_array($data) ? $data : [];
    }
}
