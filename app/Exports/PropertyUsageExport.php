<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PropertyUsageExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithEvents
{
    protected array $data;
    protected int $year;

    public function __construct(array $data, int $year)
    {
        $this->data = $data;
        $this->year = $year;
    }

    public function array(): array
    {
        return $this->data;
    }

    /** 🧭 Heading dua baris: judul besar + header kolom */
    public function headings(): array
    {
        return [
            ["Rekap Peminjaman Ruangan Tahun {$this->year}"], // judul besar
            [
                'Nama Fasilitas',
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            ],
        ];
    }

    public function title(): string
    {
        return "Rekap Tahun {$this->year}";
    }

    /** 🎨 Style tabel */
    public function styles(Worksheet $sheet)
    {
        // Gabungkan sel judul besar
        $sheet->mergeCells('A1:M1');

        // Style judul besar
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Style header kolom
        $sheet->getStyle('A2:M2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE8F1E8'], // hijau muda lembut
            ],
            'borders' => [
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ]);
    }

    /** 🧩 Border & Auto-width */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = count($this->data) + 2; // header + data
                $sheet = $event->sheet->getDelegate();

                // Border semua tabel
                $sheet->getStyle("A2:M{$rowCount}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                        'wrapText' => true,
                    ],
                ]);

                // Auto-width semua kolom
                foreach (range('A', 'M') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
