<?php
// app/Exports/BedroomsUseMonthMatrixSheet.php
namespace App\Exports;

use Carbon\Carbon;
use App\Models\DetailKamarTransaction;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromCollection;

class BedroomsUseMonthMatrixSheet implements FromCollection, WithEvents, WithTitle
{
    public function __construct(
        private Carbon $month,
        private ?string $templatePath = null
    ) {}

    public function title(): string
    {
        return $this->month->translatedFormat('M Y');
    }

    public function collection(): Collection
    {
        return collect();
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                if ($this->templatePath && file_exists($this->templatePath)) {
                    $spreadsheet = IOFactory::load($this->templatePath);
                    $event->writer->setSpreadsheet($spreadsheet);
                }
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet  = $event->sheet->getDelegate();
                $start  = (clone $this->month)->startOfMonth();
                $end    = (clone $this->month)->endOfMonth();

                // ambil data kamar approved
                $details = DetailKamarTransaction::with([
                    'kamar:id,properties_id,nama_kamar',
                    'kamar.properties:id,name,type',
                    'transaction:id,name,kegiatan,status',
                    'penghunis:id,detail_kamar_transaction_id,nama_penghuni',
                ])
                    ->whereHas('transaction', fn($q) => $q->where('status', 'approved'))
                    ->whereDate('start', '<=', $end->toDateString())
                    ->whereDate('end', '>=', $start->toDateString())
                    ->get()
                    ->filter(fn($d) => in_array($d->kamar->properties->type ?? '', ['asrama', 'paviliun']));

                if ($details->isEmpty()) {
                    $sheet->setCellValue('A1', 'Tidak ada kamar terpakai bulan ini.');
                    return;
                }

                // --- Layout header
                $sheet->setCellValue('A1', 'Rekap Kamar Terpakai');
                $sheet->setCellValue('A2', 'Periode: ' . $start->format('d M Y') . ' - ' . $end->format('d M Y'));

                $startRow = 4;
                $startCol = 1; // A

                // Header kiri tanggal
                $sheet->setCellValueByColumnAndRow($startCol, $startRow, 'Tanggal');

                // Ambil semua kamar unik
                $allKamar = $details->pluck('kamar')->unique('id')->sortBy('nama_kamar');
                $colsMap = [];
                foreach ($allKamar->values() as $i => $kamar) {
                    $colIndex = $startCol + 1 + $i;
                    $label = $kamar->properties->name . ' - ' . $kamar->nama_kamar;
                    $sheet->setCellValueByColumnAndRow($colIndex, $startRow, $label);
                    $colsMap[$kamar->id] = $colIndex;
                }

                // Baris tanggal
                $rowsMap = [];
                $r = $startRow + 1;
                $cursor = (clone $start);
                while ($cursor->lte($end)) {
                    $ymd = $cursor->format('Y-m-d');
                    $sheet->setCellValueByColumnAndRow($startCol, $r, $ymd);
                    $rowsMap[$ymd] = $r;
                    $r++;
                    $cursor->addDay();
                }

                // Hitung total hari per kamar
                $usedDaySet = []; // [kamarId][ymd]
                $totals = [];
                foreach ($allKamar as $kamar) {
                    $totals[$kamar->id] = 0;
                    $usedDaySet[$kamar->id] = [];
                }

                // --- Isi matrix
                foreach ($details as $d) {
                    $col = $colsMap[$d->kamar_id] ?? null;
                    if (!$col) continue;

                    $txStart = Carbon::parse($d->start)->max($start);
                    $txEnd   = Carbon::parse($d->end)->min($end);

                    $tanggal = "{$txStart->format('d M Y')} - {$txEnd->format('d M Y')}";
                    $penghunis = $d->penghunis->pluck('nama_penghuni')->implode(', ');

                    $text = trim(implode("\n", array_filter([
                        "Kegiatan: " . ($d->transaction->kegiatan ?? '—'),
                        "Pemesan: " . ($d->transaction->name ?? '—'),
                        "Penghuni: " . ($penghunis ?: '—'),
                        $tanggal
                    ])));

                    $day = (clone $txStart);
                    while ($day->lte($txEnd)) {
                        $ymd = $day->format('Y-m-d');
                        if (isset($rowsMap[$ymd])) {
                            $row = $rowsMap[$ymd];
                            $existing = (string) $sheet->getCellByColumnAndRow($col, $row)->getValue();
                            $value = $existing ? ($existing . "\n" . $text) : $text;
                            $sheet->setCellValueByColumnAndRow($col, $row, $value);
                            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setWrapText(true);

                            // count sekali per hari
                            if (!isset($usedDaySet[$d->kamar_id][$ymd])) {
                                $usedDaySet[$d->kamar_id][$ymd] = true;
                                $totals[$d->kamar_id] += 1;
                            }
                        }
                        $day->addDay();
                    }
                }

                // Styling border
                $lastCol = $startCol + $allKamar->count();
                $lastRow = $r - 1;
                $sheet->getStyleByColumnAndRow($startCol, $startRow, $lastCol, $lastRow)
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Lebar kolom
                $sheet->getColumnDimensionByColumn($startCol)->setWidth(12);
                for ($c = $startCol + 1; $c <= $lastCol; $c++) {
                    $sheet->getColumnDimensionByColumn($c)->setWidth(32);
                }

                // Bold header
                $sheet->getStyleByColumnAndRow($startCol, $startRow, $lastCol, $startRow)
                    ->getFont()->setBold(true);

                // --- Ringkasan total hari dipakai
                $sumRow = $lastRow + 2;
                $sheet->setCellValueByColumnAndRow($startCol, $sumRow, 'Total dipakai (hari)');
                $sheet->getStyleByColumnAndRow($startCol, $sumRow)->getFont()->setBold(true);

                $maxVal = 0;
                foreach ($allKamar as $kamar) {
                    $val = $totals[$kamar->id] ?? 0;
                    $sheet->setCellValueByColumnAndRow($colsMap[$kamar->id], $sumRow, $val);
                    $maxVal = max($maxVal, $val);
                }

                // highlight hijau untuk yang paling banyak
                if ($maxVal > 0) {
                    foreach ($allKamar as $kamar) {
                        $col = $colsMap[$kamar->id];
                        if ($totals[$kamar->id] === $maxVal) {
                            $sheet->getStyleByColumnAndRow($col, $sumRow)
                                ->getFill()->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFC6EFCE');
                            $sheet->getStyleByColumnAndRow($col, $sumRow)
                                ->getFont()->getColor()->setARGB('FF006100');
                            $sheet->getStyleByColumnAndRow($col, $sumRow)
                                ->getFont()->setBold(true);
                        }
                    }
                }

                // Freeze pane
                $sheet->freezePaneByColumnAndRow($startCol + 1, $startRow + 1);
            }
        ];
    }
}
