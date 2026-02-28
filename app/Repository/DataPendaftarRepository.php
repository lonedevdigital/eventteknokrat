<?php

namespace App\Repository;

use App\Helpers\Helper;
use App\Models\DataPendaftar;
use App\Models\HistoryPendaftaranProgram;
use App\Models\LogActivity;
use App\Models\PendaftaranPerusahaan;
use App\Models\PendaftaranProgram;
use App\Models\Program;
use App\Models\ProgramDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class DataPendaftarRepository
{
    public function generateSuratPengantar(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pendaftaran = PendaftaranProgram::findOrFail($id);
            $kodeProgramStudi = $pendaftaran->mahasiswa->kode_program_studi;
            $periodeId = $pendaftaran->periode_id;
            $programId = $pendaftaran->program_id;
            $perusahaanId = $pendaftaran->pendaftaran_perusahaan->perusahaan_id;

            $kelompokProdi = $pendaftaran->program->kelompok_prodi;
            $checkPendaftar = PendaftaranProgram::where('program_id', $programId)
                ->whereHas('periode', function ($query) use ($periodeId) {
                    $query->where('id', $periodeId);
                })
                ->whereHas('pendaftaran_perusahaan', function ($query) use ($perusahaanId) {
                    $query->where('validasi_perusahaan', 'terima');
                    $query->where('perusahaan_id', $perusahaanId);
                    $query->whereNull('nomor_surat_pengantar');
                })
                ->when($kelompokProdi != 'group', function ($query) use ($kodeProgramStudi) {
                    $query->whereHas('mahasiswa', function ($q) use ($kodeProgramStudi) {
                        $q->where('kode_program_studi', $kodeProgramStudi);
                    });
                })
                ->with(['periode', 'pendaftaran_perusahaan', 'mahasiswa'])
                ->get();
            foreach ($checkPendaftar as $key) {
                PendaftaranPerusahaan::where('id', $key->pendaftaran_perusahaan->id)
                    ->update([
                        'nomor_surat_pengantar' => $request->nomor_surat_pengantar,
                        'tanggal_surat' => $request->tanggal_surat,
                    ]);
                HistoryPendaftaranProgram::where('pendaftaran_id', $key->id)->where('perusahaan_id', $key->pendaftaran_perusahaan->perusahaan_id)->update([
                    'nomor_surat_pengantar' => $request->nomor_surat_pengantar,
                    'tanggal_surat' => $request->tanggal_surat,
                ]);
            }
            DB::commit();
            return $pendaftaran;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function downloadSurat($data)
    {
        $prodiId = $data->pendaftaran_program->mahasiswa->kode_program_studi;
        $programdetail = ProgramDetail::where('program_id', $data->pendaftaran_program->program_id)->where('kode_program_studi', $prodiId)->first();

        $file = $programdetail->template_surat_pengantar;
        $templateProcessor = new TemplateProcessor(public_path($file));

        $tanggalSurat = Carbon::parse($data->tanggal_surat)->translatedFormat('d F Y');
        $programStudi = ucwords(strtolower($data->pendaftaran_program->mahasiswa->nama_program_studi ?? ''));

        $penerima = $data->perusahaan->penerima ?? '';
        $namaPerusahaan = $data->perusahaan->nama_perusahaan ?? '';
        $alamat = $data->perusahaan->alamat ?? '';
        $kabupaten = $data->perusahaan->kabupaten ?? '';

        $mulai = $data->pendaftaran_program->periode->tanggal_mulai;
        $selesai = $data->pendaftaran_program->periode->tanggal_selesai;
        $tanggalMulai = Carbon::parse($mulai)->translatedFormat('d F');
        $tanggalSelesai = Carbon::parse($selesai)->translatedFormat('d F Y');


        $pendaftaran_perusahaan = PendaftaranPerusahaan::where('nomor_surat_pengantar', $data->nomor_surat_pengantar)->get();
        $mahasiswa = [];
        $no = 0;
        foreach ($pendaftaran_perusahaan as $key) {
            $no++;
            $mahasiswa[] = array(
                'no' => $no,
                'npm' => $key->pendaftaran_program->mahasiswa->npm_mahasiswa ?? '',
                'nama_mahasiswa' => $key->pendaftaran_program->mahasiswa->nama_mahasiswa ?? '',
                'program_studi' => $key->pendaftaran_program->mahasiswa->nama_program_studi ?? ''
            );
        }
        $templateProcessor->cloneRow('no', count($mahasiswa));
        foreach ($mahasiswa as $index => $row) {
            $i = $index + 1;
            $templateProcessor->setValue("no#{$i}", $row['no']);
            $templateProcessor->setValue("npm#{$i}", $row['npm']);
            $templateProcessor->setValue("nama_mahasiswa#{$i}", $row['nama_mahasiswa']);
            $templateProcessor->setValue("program_studi#{$i}", ucwords(strtolower($row['program_studi'])));
        }
        $templateProcessor->setValue("nomor_surat", $data->nomor_surat_pengantar);
        $templateProcessor->setValue("tanggal_surat", $tanggalSurat);
        $templateProcessor->setValue("program_studi", $programStudi);
        $templateProcessor->setValue("program_studi_kompetensi", $data->pendaftaran_program->mahasiswa->nama_program_studi ?? '');

        $templateProcessor->setValue("mulai", $tanggalMulai);
        $templateProcessor->setValue("selesai", $tanggalSelesai);

        $templateProcessor->setValue("penerima", $penerima);
        $templateProcessor->setValue("nama_perusahaan", $namaPerusahaan);
        $templateProcessor->setValue("nama_perusahaan", $namaPerusahaan);
        $templateProcessor->setValue("alamat", $alamat);
        $templateProcessor->setValue("kabupaten", $kabupaten);

        $outputFile = storage_path('app/public/surat-pengantar.docx');
        $templateProcessor->saveAs($outputFile);

        return $outputFile;
    }

    public function uploadSuratPengantar(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $fileName = 'surat-pengantar-' . time() . '.' . $file->getClientOriginalExtension();
            $path = public_path('surat-pengantar');
            $file->move($path, $fileName);
            $pendaftaran = PendaftaranProgram::findOrFail($id);
            $data = PendaftaranPerusahaan::where('nomor_surat_pengantar', $pendaftaran->pendaftaran_perusahaan->nomor_surat_pengantar)->update([
                'surat_pengantar' => $fileName,
            ]);
            DB::commit();
            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function downloasSuratTugasMahasiswa(Request $request)
    {
        $data = PendaftaranProgram::where('program_id', $request->program_id)
            ->where('periode_id', $request->periode_id)
            ->where('nomor_surat_tugas_mahasiswa', $request->nomor_surat)
            ->first();
        $pendaftaranIds = PendaftaranProgram::where('program_id', $request->program_id)
            ->where('periode_id', $request->periode_id)
            ->where('nomor_surat_tugas_mahasiswa', $request->nomor_surat)->select('id')->get();
        $prodiId = $data->mahasiswa->kode_program_studi;
        $programdetail = ProgramDetail::where('program_id', $request->program_id)->where('kode_program_studi', $prodiId)->first();

        $file = $programdetail->template_surat_tugas_mahasiswa;
        $templateProcessor = new TemplateProcessor(public_path($file));

        $tanggalSurat = Carbon::parse($data->tanggal_surat_tugas_mahasiswa)->translatedFormat('d F Y');
        $programStudi = ucwords(strtolower($data->mahasiswa->nama_program_studi ?? ''));

        $namaPerusahaan = $data->perusahaan->nama_perusahaan ?? '';

        $mulai = $data->periode->tanggal_mulai;
        $selesai = $data->periode->tanggal_selesai;
        $tanggalMulai = Carbon::parse($mulai)->translatedFormat('d F');
        $tanggalSelesai = Carbon::parse($selesai)->translatedFormat('d F Y');


        $pendaftaran_perusahaan = PendaftaranPerusahaan::whereIn('pendaftaran_id', $pendaftaranIds)->get();

        $mahasiswa = [];
        $no = 0;
        foreach ($pendaftaran_perusahaan as $key) {
            $no++;
            $mahasiswa[] = array(
                'no' => $no,
                'npm' => $key->pendaftaran_program->mahasiswa->npm_mahasiswa ?? '',
                'nama_mahasiswa' => $key->pendaftaran_program->mahasiswa->nama_mahasiswa ?? '',
                'program_studi' => $key->pendaftaran_program->mahasiswa->nama_program_studi ?? ''
            );
        }
        $templateProcessor->cloneRow('no', count($mahasiswa));
        foreach ($mahasiswa as $index => $row) {
            $i = $index + 1;
            $templateProcessor->setValue("no#{$i}", $row['no']);
            $templateProcessor->setValue("npm#{$i}", $row['npm']);
            $templateProcessor->setValue("nama_mahasiswa#{$i}", $row['nama_mahasiswa']);
            $templateProcessor->setValue("program_studi#{$i}", ucwords(strtolower($row['program_studi'])));
        }
        $templateProcessor->setValue("nomor_surat", $data->nomor_surat_tugas_mahasiswa);
        $templateProcessor->setValue("tanggal_surat", $tanggalSurat);
        $templateProcessor->setValue("program_studi", $programStudi);

        $templateProcessor->setValue("mulai", $tanggalMulai);
        $templateProcessor->setValue("selesai", $tanggalSelesai);

        $templateProcessor->setValue("nama_perusahaan", $namaPerusahaan);

        $outputFile = storage_path('app/public/surat-tugas-mahasiswa.docx');
        $templateProcessor->saveAs($outputFile);

        return $outputFile;
    }

    public function downloasSuratEkspedisi(Request $request)
    {
        $data = PendaftaranProgram::where('program_id', $request->program_id)
            ->where('periode_id', $request->periode_id)
            ->where('nomor_surat_tugas_mahasiswa', $request->nomor_surat)
            ->first();
        $pendaftaran = PendaftaranProgram::where('program_id', $request->program_id)
            ->where('periode_id', $request->periode_id)
            ->where('nomor_surat_tugas_mahasiswa', $request->nomor_surat)
            ->get();

        $program = Program::findOrFail($request->program_id);
        $file = $program->template_ekspedisi;
        $templateProcessor = new TemplateProcessor(public_path($file));

        $tanggalSurat = Carbon::parse($data->tanggal_surat_tugas_mahasiswa)->translatedFormat('d F Y');
        $programStudi = ucwords(strtolower($data->mahasiswa->nama_program_studi ?? ''));

        $namaPerusahaan = $data->perusahaan->nama_perusahaan ?? '';

        $mulai = $data->periode->tanggal_mulai;
        $selesai = $data->periode->tanggal_selesai;
        $tanggalMulai = Carbon::parse($mulai)->translatedFormat('d F');
        $tanggalSelesai = Carbon::parse($selesai)->translatedFormat('d F Y');

        $pendaftaran_perusahaan = PendaftaranPerusahaan::where('pendaftaran_id', $data->id)->first();


        $mahasiswa = [];
        $no = 0;
        foreach ($pendaftaran as $key) {
            $no++;
            $mahasiswa[] = array(
                'no' => $no,
                'npm' => $key->mahasiswa->npm_mahasiswa ?? '',
                'nama_mahasiswa' => $key->mahasiswa->nama_mahasiswa ?? '',
                'program_studi' => $key->mahasiswa->nama_program_studi ?? '',
                'gender' => $key->mahasiswa->gender ?? '',
                'telepon' => $key->mahasiswa->no_telepon ?? '',
            );
        }
        $templateProcessor->cloneRow('no', count($mahasiswa));
        foreach ($mahasiswa as $index => $row) {
            $i = $index + 1;
            if ($row['gender'] == 'L') {
                $gender = "Laki-Laki";
            } else if ($row['gender'] == 'P') {
                $gender = "Perempuan";
            }
            $templateProcessor->setValue("no#{$i}", $row['no']);
            $templateProcessor->setValue("npm#{$i}", $row['npm']);
            $templateProcessor->setValue("nama_mahasiswa#{$i}", $row['nama_mahasiswa']);
            $templateProcessor->setValue("program_studi#{$i}", ucwords(strtolower($row['program_studi'])));
            $templateProcessor->setValue("gender#{$i}", $gender ?? '');
            $templateProcessor->setValue("telepon#{$i}", ucwords(strtolower($row['telepon'])));
        }

        $semester = Helper::semester($data->semester_id);
        $tahunAkademik = Helper::tahunAkedemik($data->semester_id);

        $namaPerusahaan = $data->pendaftaran_perusahaan->perusahaan->nama_perusahaan ?? '';
        $alamatPerusahaan = $data->pendaftaran_perusahaan->perusahaan->alamat ?? '';
        $mulai = $data->periode->tanggal_mulai;
        $selesai = $data->periode->tanggal_selesai;
        $tanggalMulai = Carbon::parse($mulai)->translatedFormat('d F Y');
        $tanggalSelesai = Carbon::parse($selesai)->translatedFormat('d F Y');
        $namaPembimbing = $data->pembimbing_laporan->nama_dosen ?? '';


        $templateProcessor->setValue("semester", $semester);
        $templateProcessor->setValue("tahun_akademik", $tahunAkademik);

        $templateProcessor->setValue("nama_perusahaan", $namaPerusahaan);
        $templateProcessor->setValue("alamat_perusahaan", $alamatPerusahaan);
        $templateProcessor->setValue("mulai", $tanggalMulai);
        $templateProcessor->setValue("selesai", $tanggalSelesai);

        $templateProcessor->setValue("nama_pembimbing", $namaPembimbing);

        $outputFile = storage_path('app/public/surat-ekspedisi.docx');
        $templateProcessor->saveAs($outputFile);

        return $outputFile;
    }

    public function downloasSuratTugasDosen(Request $request)
    {
        $data = PendaftaranProgram::where('program_id', $request->program_id)
            ->where('periode_id', $request->periode_id)
            ->where('pembimbing_laporan_id', $request->dosen_id)
            ->first();
        $prodiId = $data->pembimbing_laporan->kode_program_studi;
        $programdetail = ProgramDetail::where('program_id', $request->program_id)->where('kode_program_studi', $prodiId)->first();
        $file = $programdetail->template_surat_tugas_dosen;
        $templateProcessor = new TemplateProcessor(public_path($file));

        $tanggalSurat = Carbon::parse($data->tanggal_surat_tugas_dosen)->translatedFormat('d F Y');

        $mulai = $data->periode->tanggal_mulai;
        $selesai = $data->periode->tanggal_selesai;
        $tanggalMulai = Carbon::parse($mulai)->translatedFormat('d F');
        $tanggalSelesai = Carbon::parse($selesai)->translatedFormat('d F Y');

        $templateProcessor->setValue("nomor_surat", $data->nomor_surat_tugas_dosen);
        $templateProcessor->setValue("nama_dosen", $data->pembimbing_laporan->nama_dosen);
        $templateProcessor->setValue("tanggal_surat", $tanggalSurat);

        $templateProcessor->setValue("mulai", $tanggalMulai);
        $templateProcessor->setValue("selesai", $tanggalSelesai);

        $outputFile = storage_path('app/public/surat-tugas-dosen.docx');
        $templateProcessor->saveAs($outputFile);

        return $outputFile;
    }
}
