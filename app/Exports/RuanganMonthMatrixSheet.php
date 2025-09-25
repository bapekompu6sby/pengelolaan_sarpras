<?php
// app/Exports/RuanganMonthMatrixSheet.php
namespace App\Exports;

use Carbon\Carbon;
use App\Models\Properties;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\FromCollection;

class RuanganMonthMatrixSheet implements FromCollection, WithEvents, WithTitle
{
    public function __construct(
        private Carbon $month,
        private ?string $templatePath = null
    ) {}

    public function title(): string
    {
        // Pastikan locale ID kalau mau “Sep 2025” versi Indonesia
        //0 \Carbon\Carbon::setLocale('id'); bisa kamu taruh di AppServiceProvider boot()
        return $this->month->translatedFormat('M Y');
    }


    public function collection(): Collection
    {
        return collect(); // data diisi manual di AfterSheet
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
                $sheet = $event->sheet->getDelegate();

                // Range tanggal bulan ini
                $start = (clone $this->month)->startOfMonth();
                $end   = (clone $this->month)->endOfMonth();

                // 1) SEMUA PROPERTIES
                $allProps = Properties::orderBy('name')->get(['id', 'name']);
                if ($allProps->isEmpty()) {
                    $sheet->setCellValue('A1', 'Tidak ada data property.');
                    return;
                }

                // 2) TRANSAKSI APPROVED DI BULAN ITU
                $txs = Transaction::select('instansi', 'kegiatan', 'start', 'end', 'property_id')
                    ->where('status', 'approved')
                    ->whereDate('start', '<=', $end->toDateString())
                    ->whereDate('end',   '>=', $start->toDateString())
                    ->orderBy('start')
                    ->get();

                // --- Layout ---
                $sheet->setCellValue('A1', 'Rekap Peminjaman Ruangan');
                $sheet->setCellValue('A2', 'Periode: ' . $start->format('d M Y') . ' - ' . $end->format('d M Y'));

                $startRow = 4;
                $startCol = 1; // A

                // Header kiri (Tanggal)
                $sheet->setCellValueByColumnAndRow($startCol, $startRow, 'Tanggal');

                // Header properties + peta kolom
                $colsMap = [];              // property_id => colIndex
                $propOrder = [];            // index => ['id'=>..,'name'=>..] (untuk iterasi berurutan)
                foreach ($allProps->values() as $i => $p) {
                    $colIndex = $startCol + 1 + $i; // mulai dari kolom B
                    $sheet->setCellValueByColumnAndRow($colIndex, $startRow, $p->name);
                    $colsMap[$p->id] = $colIndex;
                    $propOrder[$i] = ['id' => $p->id, 'name' => $p->name, 'col' => $colIndex];
                }

                // Baris tanggal
                $rowsMap = []; // 'Y-m-d' => rowIndex
                $r = $startRow + 1;
                $cursor = (clone $start);
                while ($cursor->lte($end)) {
                    $ymd = $cursor->format('Y-m-d');
                    $sheet->setCellValueByColumnAndRow($startCol, $r, $ymd);
                    $rowsMap[$ymd] = $r;
                    $r++;
                    $cursor->addDay();
                }

                // --- Isi matrix + hitung total hari dipinjam per property ---
                // gunakan set untuk menghindari double count pada (property, hari) yang sama
                $usedDaySet = [];  // [$propId][$ymd] = true
                $totals     = [];  // $totals[$propId] = jumlah hari

                foreach ($propOrder as $info) {
                    $totals[$info['id']] = 0;
                    $usedDaySet[$info['id']] = [];
                }

                foreach ($txs as $t) {
                    $col = $colsMap[$t->property_id] ?? null;
                    if (!$col) continue;

                    $txStart = Carbon::parse($t->start)->max($start);
                    $txEnd   = Carbon::parse($t->end)->min($end);

                    $text = trim(implode(' / ', array_filter([
                        $t->instansi,
                        $t->kegiatan,
                    ])));

                    $day = (clone $txStart);
                    while ($day->lte($txEnd)) {
                        $ymd = $day->format('Y-m-d');
                        if (isset($rowsMap[$ymd])) {
                            $row = $rowsMap[$ymd];

                            // tulis/append ke sel
                            $existing = (string) $sheet->getCellByColumnAndRow($col, $row)->getValue();
                            $value = $existing ? ($existing . "\n" . $text) : $text;
                            $sheet->setCellValueByColumnAndRow($col, $row, $value);
                            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setWrapText(true);

                            // tandai 1 hari terpakai untuk property ini (hindari double count)
                            if (!isset($usedDaySet[$t->property_id][$ymd])) {
                                $usedDaySet[$t->property_id][$ymd] = true;
                                $totals[$t->property_id] += 1;
                            }
                        }
                        $day->addDay();
                    }
                }

                // Styling dasar tabel utama
                $lastCol = $startCol + $allProps->count();
                $lastRow = $r - 1;

                // Tebalkan header (row 4)
                $sheet->getStyleByColumnAndRow($startCol, $startRow, $lastCol, $startRow)
                    ->getFont()->setBold(true);

                // Border tipis seluruh blok data
                $sheet->getStyleByColumnAndRow($startCol, $startRow, $lastCol, $lastRow)
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Lebar kolom
                $sheet->getColumnDimensionByColumn($startCol)->setWidth(12); // Tanggal
                for ($c = $startCol + 1; $c <= $lastCol; $c++) {
                    $sheet->getColumnDimensionByColumn($c)->setWidth(28);
                }

                // --- BARIS RINGKASAN DI BAWAH TABEL ---
                $sumRowLabel = $lastRow + 2; // satu baris kosong pemisah
                $sheet->setCellValueByColumnAndRow($startCol, $sumRowLabel, 'Total dipinjam (hari)');
                $sheet->getStyleByColumnAndRow($startCol, $sumRowLabel)->getFont()->setBold(true);

                // tulis total per property di baris ini
                $maxVal = 0;
                foreach ($propOrder as $info) {
                    $propId = $info['id'];
                    $col    = $info['col'];
                    $val    = $totals[$propId] ?? 0;

                    $sheet->setCellValueByColumnAndRow($col, $sumRowLabel, $val);
                    $maxVal = max($maxVal, $val);
                }

                // Border & format baris ringkasan
                $sheet->getStyleByColumnAndRow($startCol, $sumRowLabel, $lastCol, $sumRowLabel)
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // --- HIGHLIGHT yang PALING BANYAK (warna hijau) ---
                if ($maxVal > 0) {
                    foreach ($propOrder as $info) {
                        $propId = $info['id'];
                        $col    = $info['col'];
                        if (($totals[$propId] ?? 0) === $maxVal) {
                            // hijau lembut di sel total
                            $sheet->getStyleByColumnAndRow($col, $sumRowLabel)
                                ->getFill()->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFC6EFCE'); // light green
                            $sheet->getStyleByColumnAndRow($col, $sumRowLabel)
                                ->getFont()->getColor()->setARGB('FF006100'); // dark green
                            $sheet->getStyleByColumnAndRow($col, $sumRowLabel)
                                ->getFont()->setBold(true);
                        }
                    }
                }

                // (opsional) Freeze pane biar header & kolom tanggal tetap terlihat
                $sheet->freezePaneByColumnAndRow($startCol + 1, $startRow + 1); // freeze di B5
            },
        ];
    }
}
