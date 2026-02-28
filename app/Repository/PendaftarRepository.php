<?php

namespace App\Repository;

use App\Models\HistoryPendaftaranProgram;
use App\Models\PendaftaranPerusahaan;
use App\Models\PendaftaranProgram;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftarRepository
{

    public function konfirmasiPerusahaan(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pendaftaran = PendaftaranProgram::findOrFail($id);
            $pendaftaranPerusahaan = $pendaftaran->pendaftaran_perusahaan;

            $noSurat = $pendaftaranPerusahaan->nomor_surat_pengantar ?? '';
            $perusahaanId = $pendaftaranPerusahaan->perusahaan_id ?? '';
            $programId = $pendaftaran->program_id ?? '';

            PendaftaranPerusahaan::where('perusahaan_id', $perusahaanId)
                ->where('nomor_surat_pengantar', $noSurat)
                ->update([
                    'surat_balasan' => $request->balasan,
                    'status' => $request->status,
                ]);

            if ($request->status === 'terima') {
                PendaftaranPerusahaan::where('perusahaan_id', $perusahaanId)
                    ->where('nomor_surat_pengantar', $noSurat)
                    ->get()
                    ->each(function ($item) {
                        PendaftaranProgram::find($item->pendaftaran_id)?->update([
                            'status_pendaftaran' => 'terima',
                        ]);
                    });
            }
            HistoryPendaftaranProgram::where('program_id', $programId)->where('perusahaan_id', $perusahaanId)->where('nomor_surat_pengantar', $noSurat)->update([
                'status' => $request->status,
            ]);
            DB::commit();
            $msg = "Berhasil Konfirmasi Perusahaan";
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = "Gagal Konfirmasi Perusahaan";
        }
        return $msg;
    }
}
