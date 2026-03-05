<?php
// app/Exports/RuanganSimpleExport.php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RuanganSimpleExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    public function __construct(
        private Carbon $start,
        private Carbon $end
    ) {}

    // ── Data ────────────────────────────────────────────────
    public function collection()
    {
        return Transaction::with('properties')
            // ->where('type', 'ruangan')                         // sesuaikan kolom/value
            ->whereBetween('start', [$this->start, $this->end])
            ->orderBy('start')
            ->get();
    }

    // ── Header kolom ────────────────────────────────────────
    public function headings(): array
    {
        return [
            'No',
            'Nama Pemesan',
            'No. HP',
            'Instansi',
            'Kegiatan',
            'Ruangan',
            'Tanggal Mulai',
            'Tanggal Selesai',
        ];
    }

    // ── Mapping tiap baris ──────────────────────────────────
    public function map($t): array
    {
        static $no = 0;
        $no++;

        $statusMap = [
            'approved'        => 'Disetujui',
            'pending'         => 'Menunggu',
            'rejected'        => 'Ditolak',
            'cancelled'       => 'Dibatalkan',
            'waiting_payment' => 'Menunggu Pembayaran',
        ];

        return [
            $no,
            $t->name,
            $t->phone_number ?? '-',
            ucfirst($t->instansi),
            $t->kegiatan,
            $t->properties->name ?? '-',
            Carbon::parse($t->start)->format('d/m/Y'),
            Carbon::parse($t->end)->format('d/m/Y'),

        ];
    }

    // ── Lebar kolom ─────────────────────────────────────────
    public function columnWidths(): array
    {
        return [
            'A' => 5,    // No
            'B' => 25,   // Nama
            'C' => 16,   // HP
            'D' => 22,   // Instansi
            'E' => 35,   // Kegiatan
            'F' => 25,   // Ruangan
            'G' => 15,   // Tgl Mulai
            'H' => 15,   // Tgl Selesai
            'I' => 22,   // Status
        ];
    }

    // ── Style ────────────────────────────────────────────────
    public function styles(Worksheet $sheet): array
    {
        // Judul di baris 1 (sebelum heading DataTables)
        $sheet->insertNewRowBefore(1, 2);

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'Rekap Peminjaman Ruangan');
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue(
            'A2',
            'Periode: ' . $this->start->translatedFormat('d M Y') . ' – ' . $this->end->translatedFormat('d M Y')
        );

        return [
            // Judul utama
            1 => [
                'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF0F3D7A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Sub-judul periode
            2 => [
                'font'      => ['size' => 10, 'color' => ['argb' => 'FF475467']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Row header kolom (sekarang jadi baris 3)
            3 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0F3D7A'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    // ── Nama sheet ───────────────────────────────────────────
    public function title(): string
    {
        return 'Rekap ' . $this->start->format('M Y')
            . ($this->start->format('Y-m') !== $this->end->format('Y-m')
                ? ' - ' . $this->end->format('M Y')
                : '');
    }
}
