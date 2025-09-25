<?php
// app/Exports/RuanganMonthMatrixSheet.php
namespace App\Exports;

use Carbon\Carbon;
use App\Models\Properties;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Concerns\FromCollection;

class RuanganMonthMatrixSheet implements FromCollection, WithEvents, WithTitle
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

                // 1) AMBIL SEMUA PROPERTIES (header selalu lengkap)
                //    pakai id + name agar mappingnya stabil
                $allProps = Properties::orderBy('name')->get(['id', 'name']); // <-- PENTING
                if ($allProps->isEmpty()) {
                    // kalau benar-benar tidak ada property, tulis pesan singkat & keluar
                    $sheet->setCellValue('A1', 'Tidak ada data property.');
                    return;
                }

                // 2) Ambil transaksi di bulan ini (cukup fields yang dipakai)
                $txs = Transaction::select('instansi', 'kegiatan', 'start', 'end', 'property_id')
                    ->whereDate('start', '<=', $end->toDateString())
                    ->whereDate('end',   '>=', $start->toDateString())
                    ->orderBy('start')
                    ->get();

                // --- Layout ---
                $sheet->setCellValue('A1', 'Rekap Peminjaman Ruangan');
                $sheet->setCellValue('A2', 'Periode: ' . $start->format('d M Y') . ' - ' . $end->format('d M Y'));

                $startRow = 4;
                $startCol = 1; // A

                // Header kiri (tanggal)
                $sheet->setCellValueByColumnAndRow($startCol, $startRow, 'Tanggal'); // A4

                // HEADER PROPERTIES: tulis SEMUA property (kolom B, C, dst)
                // colsMap berdasarkan property_id -> colIndex
                $colsMap = [];
                foreach ($allProps->values() as $i => $p) {
                    $colIndex = $startCol + 1 + $i; // mulai dari kolom B
                    $sheet->setCellValueByColumnAndRow($colIndex, $startRow, $p->name);
                    $colsMap[$p->id] = $colIndex;
                }

                // Tulis tanggal ke kolom A (baris 5..)
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

                // --- Isi data ke matrix ---
                foreach ($txs as $t) {
                    // dapatkan kolom dari property_id (bukan dari nama relasi)
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

                            $existing = (string) $sheet
                                ->getCellByColumnAndRow($col, $row)
                                ->getValue();

                            $value = $existing ? ($existing . "\n" . $text) : $text;

                            $sheet->setCellValueByColumnAndRow($col, $row, $value);
                            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setWrapText(true);
                        }
                        $day->addDay();
                    }
                }

                // Styling dasar (kalau tidak ada styling dari template)
                $lastCol = $startCol + $allProps->count();
                $lastRow = $r - 1;

                // Bold header (row 4)
                $sheet->getStyleByColumnAndRow($startCol, $startRow, $lastCol, $startRow)
                    ->getFont()->setBold(true);

                // Border tipis seluruh tabel (header + data)
                $sheet->getStyleByColumnAndRow($startCol, $startRow, $lastCol, $lastRow)
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Lebar kolom
                $sheet->getColumnDimensionByColumn($startCol)->setWidth(12); // Tanggal
                for ($c = $startCol + 1; $c <= $lastCol; $c++) {
                    $sheet->getColumnDimensionByColumn($c)->setWidth(28);
                }
            },
        ];
    }
}
