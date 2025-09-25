<?php
// app/Exports/RuanganMultiMonthExport.php
namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RuanganMultiMonthExport implements WithMultipleSheets
{
    public function __construct(
        private Carbon $startMonth,
        private int $months = 3,
        private ?string $templatePath = null // storage_path ke template kalau ada
    ) {}

    public function sheets(): array
    {
        $sheets = [];
        for ($i = 0; $i < $this->months; $i++) {
            $month = (clone $this->startMonth)->addMonths($i);
            $sheets[] = new RuanganMonthMatrixSheet($month, $this->templatePath);
        }
        return $sheets;
    }
}
