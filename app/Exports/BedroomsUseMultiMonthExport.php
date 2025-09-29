<?php
// app/Exports/BedroomsUseMultiMonthExport.php
namespace App\Exports;

use Carbon\Carbon;
use App\Exports\BedroomsUseMonthMatrixSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BedroomsUseMultiMonthExport implements WithMultipleSheets
{
    protected $start;
    protected $count;
    protected $templatePath;

    public function __construct(Carbon $start, int $count, ?string $templatePath = null)
    {
        $this->start        = $start->copy()->startOfMonth();
        $this->count        = $count;
        $this->templatePath = $templatePath;
    }

    public function sheets(): array
    {
        $sheets = [];

        for ($i = 0; $i < $this->count; $i++) {
            $month = $this->start->copy()->addMonths($i);

            $sheets[] = new BedroomsUseMonthMatrixSheet($month, $this->templatePath);
        }

        return $sheets;
    }
}
