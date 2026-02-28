<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PendaftarExport implements FromArray, WithStyles, WithEvents, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $header = [
            [
                'NO',
                'PERUSAHAAN',
                'NPM',
                'NAMA',
                'PROGRAM STUDI',
                'NO TELP',
                'NIK',
            ]
        ];
        return array_merge($header, $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);
        return [];
    }

    public function title(): string
    {
        return 'DATA PENDAFTAR';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = count($this->data);
                $range = 'A1:G' . ($rowCount + 1);
                $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(25);
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $maxRow = count($this->data) + 1;
                $sheet->getStyle('F2:F' . $maxRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);
                $sheet->getStyle('G2:G' . $maxRow)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                foreach ($this->data as $rowIndex => $row) {
                    $rowNumber = $rowIndex + 2;
                    $nikCell = $sheet->getCell('G' . $rowNumber);
                    $nikCell->setValueExplicit($row['nik'], DataType::TYPE_STRING);

                    $telpCell = $sheet->getCell('F' . $rowNumber);
                    $telpCell->setValueExplicit($row['telp'], DataType::TYPE_STRING);
                }
            }

        ];
    }
}
