<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LombaPesertaExport implements FromArray, WithStyles, WithEvents, WithTitle
{
    protected Event $event;

    /** @var array<int, array{nama_cabang: ?string, rows: array}> */
    protected array $groups;

    protected int $totalPeserta;

    /** Baris (index 1-based) yang merupakan header tabel per-cabang, untuk styling. */
    protected array $headerRows = [];

    public function __construct(Event $event, array $groups, int $totalPeserta)
    {
        $this->event = $event;
        $this->groups = $groups;
        $this->totalPeserta = $totalPeserta;
    }

    public function array(): array
    {
        $rows = [
            [$this->event->nama_event],
            ['Total Keseluruhan Peserta: ' . $this->totalPeserta],
            [],
        ];

        $rowNumber = count($rows);

        foreach ($this->groups as $group) {
            $rowNumber++;
            $rows[] = [$group['nama_cabang'] ? 'Cabang Lomba: ' . $group['nama_cabang'] : 'Peserta'];

            $rowNumber++;
            $rows[] = ['Nama Tim / Mahasiswa', 'NPM', 'Anggota Tim', 'Cabang Lomba'];
            $this->headerRows[] = $rowNumber;

            foreach ($group['rows'] as $row) {
                $rowNumber++;
                $rows[] = [$row['nama'], $row['npm'] ?? '', $row['anggota'] ?? '', $row['cabang']];
            }

            $rowNumber++;
            $rows[] = [];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);

        foreach ($this->headerRows as $rowNumber) {
            $sheet->getStyle('A' . $rowNumber . ':D' . $rowNumber)->getFont()->setBold(true);
            $sheet->getStyle('A' . $rowNumber . ':D' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }

    public function title(): string
    {
        return 'Data Peserta';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getColumnDimension('C')->setWidth(40);

                // Aktifkan wrap text untuk kolom Anggota Tim (list numbering multi-baris)
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('C1:C' . $lastRow)->getAlignment()->setWrapText(true);

                // Border pada baris data (header sudah diberi border via headerRows)
                foreach ($this->headerRows as $headerRow) {
                    $dataStart = $headerRow + 1;
                    $dataRow = $dataStart;
                    while ($dataRow <= $lastRow) {
                        $cellValue = $sheet->getCell('A' . $dataRow)->getValue();
                        if ($cellValue === null || $cellValue === '') {
                            break;
                        }
                        $sheet->getStyle('A' . $dataRow . ':D' . $dataRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                        $dataRow++;
                    }
                }
            },
        ];
    }
}
