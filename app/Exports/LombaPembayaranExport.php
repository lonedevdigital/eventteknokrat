<?php

namespace App\Exports;

use App\Models\Event;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LombaPembayaranExport implements FromArray, WithStyles, WithEvents, WithTitle
{
    protected Event $event;
    protected Collection $rows;

    public function __construct(Event $event, Collection $rows)
    {
        $this->event = $event;
        $this->rows = $rows;
    }

    public function array(): array
    {
        $header = [
            [$this->event->nama_event],
            ['Total Peserta: ' . $this->rows->count()],
            [],
            ['Jenis Pendaftaran', 'Cabang Lomba', 'Nama Mahasiswa / Nama Tim', 'NPM', 'Status Pembayaran'],
        ];

        $data = $this->rows->map(fn ($row) => [
            $row['jenis_pendaftaran'],
            $row['cabang_lomba'],
            $row['nama'],
            $row['npm'],
            $row['status_pembayaran'],
        ])->all();

        return array_merge($header, $data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        $sheet->getStyle('A4:E4')->getAlignment()->setHorizontal('center');

        return [];
    }

    public function title(): string
    {
        return 'Rekap Pembayaran';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = 4 + $this->rows->count();

                $sheet->getStyle('A4:E' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
