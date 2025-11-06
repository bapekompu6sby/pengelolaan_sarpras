<?php

namespace App\Http\Controllers;


use Carbon\Carbon;

use App\Models\Properties;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\PropertyUsageExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{

    public function bukuPanduan()
    {
        return view('components.user_guide');
    }
    public function index()
    {
        $today = now()->toDateString();

        //aula
        $aulaStock = Properties::where('type', 'aula')->sum('unit');

        $aulaNotAvailable = Transaction::whereHas('properties', function ($query) {
            $query->where('type', 'aula');
        })

            ->where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->where('status', 'approved')
            ->sum('ordered_unit');


        $availableAula = $aulaStock - $aulaNotAvailable;



        //kelas
        $kelasStock = Properties::where('type', 'kelas')->sum('unit');

        $kelasNotAvailable = Transaction::whereHas('properties', function ($query) {
            $query->where('type', 'kelas');
        })
            ->where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->where('status', 'approved')
            ->sum('ordered_unit');

        $availableKelas = $kelasStock - $kelasNotAvailable;



        //asrama
        $asramaStock = Properties::where('type', 'asrama')->sum('unit');

        $asramaNotAvailable = Transaction::whereHas('properties', function ($query) {
            $query->where('type', 'asrama');
        })
            ->where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->where('status', 'approved')
            ->sum('ordered_unit');

        $availableAsrama = $asramaStock - $asramaNotAvailable;



        //paviliun
        $paviliunStock = Properties::where('type', 'paviliun')->sum('unit');

        $paviliunNotAvailable = Transaction::whereHas('properties', function ($query) {
            $query->where('type', 'paviliun');
        })
            ->where('status', 'approved')
            ->where(function ($query) use ($today) {
                $query->whereDate('start', '<=', $today)
                    ->whereDate('end', '>=', $today);
            })
            ->sum('ordered_unit');


        $availablePaviliun = $paviliunStock - $paviliunNotAvailable;



        $events = Transaction::where('status', 'approved')
            ->where(function ($q) use ($today) {
                $q->where(function ($q2) use ($today) {

                    $q2->where('start', '<=', $today)
                        ->where('end', '>=', $today);
                });
            })
            ->orderBy('start', 'desc')
            ->take(10)
            ->get();

        return view('welcome', [
            'availableAula' => $availableAula,
            'availableKelas' => $availableKelas,
            'availableAsrama' => $availableAsrama,
            'availablePaviliun' => $availablePaviliun,
            'aulaNotAvailable' => $aulaNotAvailable,
            'kelasNotAvailable' => $kelasNotAvailable,
            'asramaNotAvailable' => $asramaNotAvailable,
            'paviliunNotAvailable' => $paviliunNotAvailable,
            'aulaStock' => $aulaStock,
            'kelasStock' => $kelasStock,
            'asramaStock' => $asramaStock,
            'paviliunStock' => $paviliunStock,
            'events' => $events,
        ]);
    }

    // tabel kegiatan
    public function tabelKegiatan()
    {

        $today = now('Asia/Jakarta')->toDateString();

        $events = Transaction::where('status', 'approved')
            ->where(function ($q) use ($today) {
                $q->where(function ($q2) use ($today) {

                    $q2->where('start', '<=', $today)
                        ->where('end', '>=', $today);
                });
            })
            ->orderBy('start', 'desc')
            ->take(10)
            ->get();



        return view('tabel_kegiatan', [
            'events' => $events,
        ]);
    }

    public function show_event()
    {
        $today = now('Asia/Jakarta')->toDateString();

        $events = Transaction::where('status', 'approved')
            ->where(function ($q) use ($today) {
                $q->where(function ($q2) use ($today) {

                    $q2->where('start', '<=', $today)
                        ->where('end', '>=', $today);
                });
            })
            ->orderBy('start', 'desc')
            ->take(10)
            ->get();

        return response()->json($events);
    }


    public function dashboardAdmin(Request $request)
    {
        // ====== FILTERS ======
        // Robust: jangan andalkan boolean() (beda versi Laravel), pakai cek string "1"
        $useRange = $request->has('use_range') && (string)$request->get('use_range') === '1';
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', 0); // 0 = semua bulan

        // Daftar tahun (fallback 5 tahun terakhir kalau kosong)
        $years = Transaction::query()
            ->selectRaw('YEAR(`start`) as y')
            ->whereNotNull('start')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->toArray();

        if (empty($years)) {
            $yNow  = now()->year;
            $years = range($yNow, $yNow - 4);
        }

        // Formatter aman untuk "Bulan Tahun"
        $fmtMY = function (\Carbon\Carbon $c) {
            // Kalau Carbon v2 + locale id tersedia
            if (method_exists($c, 'isoFormat')) {
                return $c->locale('id')->isoFormat('MMMM YYYY'); // contoh: "November 2025"
            }
            // Fallback universal (Inggris)
            return $c->format('F Y');
        };

        // ====== PERIODE HITUNG ======
        if ($useRange) {
            // Mode rentang bulan
            $startMonthStr = $request->get('start_month', now()->format('Y-m')); // "YYYY-MM"
            $endMonthStr   = $request->get('end_month', $startMonthStr);

            $startMonth = \Carbon\Carbon::createFromFormat('Y-m', $startMonthStr)->startOfMonth();
            $endMonth   = \Carbon\Carbon::createFromFormat('Y-m', $endMonthStr)->endOfMonth();

            // Tukar kalau kebalik
            if ($endMonth->lt($startMonth)) {
                [$startMonth, $endMonth] = [
                    $endMonth->copy()->startOfMonth(),
                    $startMonth->copy()->endOfMonth()
                ];
            }

            $periodStart = $startMonth->copy()->startOfDay();
            $periodEnd   = $endMonth->copy()->endOfDay();

            // Label periode
            $periodLabel = $startMonth->isSameMonth($endMonth)
                ? $fmtMY($startMonth)
                : $fmtMY($startMonth) . ' – ' . $fmtMY($endMonth);

            // Kompatibilitas UI lama
            $month = 0;
            $year  = $startMonth->year;
        } else {
            // Mode tahun/bulan tunggal
            if ($month >= 1 && $month <= 12) {
                $periodStart = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
                $periodEnd   = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
                $periodLabel = $fmtMY($periodStart);
            } else {
                $periodStart = \Carbon\Carbon::create($year, 1, 1)->startOfDay();
                $periodEnd   = \Carbon\Carbon::create($year, 12, 31)->endOfDay();
                $periodLabel = 'Jan–Des ' . $year;
            }
        }

        // ====== MASTER PROPERTIES ======
        $typesWanted = ['aula', 'paviliun', 'kelas', 'asrama', 'fasilitas'];

        $props = DB::table('properties')
            ->selectRaw('LOWER(`type`) as prop_type, `id`, `name`')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('prop_type');

        $charts = [];
        foreach ($typesWanted as $t) {
            $labels = ($props->has($t) ? $props[$t]->pluck('name')->all() : []);
            $ids    = ($props->has($t) ? $props[$t]->pluck('id')->all()   : []);
            $charts[$t] = [
                'labels' => $labels,
                'ids'    => $ids,
                'data'   => array_fill(0, count($labels), 0),
            ];
        }

        // ====== HITUNG HARI TERPESAN (approved) DI PERIODE (overlap) ======
        $counts = Transaction::query()
            ->join('properties', 'properties.id', '=', 'transactions.property_id')
            ->where('transactions.status', 'approved')
            ->whereDate('transactions.start', '<=', $periodEnd->toDateString())
            ->whereDate('transactions.end',   '>=', $periodStart->toDateString())
            ->selectRaw("
            LOWER(properties.type) as prop_type,
            properties.id as property_id,
            SUM(
                CASE
                    WHEN LEAST(transactions.`end`, ?) < GREATEST(transactions.`start`, ?) THEN 0
                    ELSE DATEDIFF(LEAST(transactions.`end`, ?), GREATEST(transactions.`start`, ?)) + 1
                END
            ) as total_days
        ", [
                $periodEnd->toDateString(),
                $periodStart->toDateString(),
                $periodEnd->toDateString(),
                $periodStart->toDateString()
            ])
            ->groupBy('prop_type', 'property_id')
            ->get();

        foreach ($counts as $row) {
            $t = $row->prop_type;
            if (!isset($charts[$t])) continue;

            $idx = array_search((int)$row->property_id, $charts[$t]['ids'], true);
            if ($idx !== false) {
                $charts[$t]['data'][$idx] = (int) $row->total_days;
            }
        }

        // Bersihkan mapping id sebelum dikirim ke JS
        foreach ($charts as $k => $v) {
            unset($charts[$k]['ids']);
        }

        // ====== ITEMS TERBARU (opsional) ======
        $items = Transaction::with('properties')
            ->where('status', 'approved')
            ->latest('start')
            ->take(10)
            ->get();

        // ====== STOCK & TERSEDIA HARI INI ======
        $today = now()->toDateString();

        $aulaStock = Properties::where('type', 'aula')->sum('unit');
        $aulaNotAvailable = Transaction::whereHas('properties', fn($q) => $q->where('type', 'aula'))
            ->where('start', '<=', $today)
            ->where('end',   '>=', $today)
            ->where('status', 'approved')
            ->sum('ordered_unit');
        $availableAula = $aulaStock - $aulaNotAvailable;

        $kelasStock = Properties::where('type', 'kelas')->sum('unit');
        $kelasNotAvailable = Transaction::whereHas('properties', fn($q) => $q->where('type', 'kelas'))
            ->where('start', '<=', $today)
            ->where('end',   '>=', $today)
            ->where('status', 'approved')
            ->sum('ordered_unit');
        $availableKelas = $kelasStock - $kelasNotAvailable;

        $asramaStock = Properties::where('type', 'asrama')->sum('unit');
        $asramaNotAvailable = Transaction::whereHas('properties', fn($q) => $q->where('type', 'asrama'))
            ->where('start', '<=', $today)
            ->where('end',   '>=', $today)
            ->where('status', 'approved')
            ->sum('ordered_unit');
        $availableAsrama = $asramaStock - $asramaNotAvailable;

        $paviliunStock = Properties::where('type', 'paviliun')->sum('unit');
        $paviliunNotAvailable = Transaction::whereHas('properties', fn($q) => $q->where('type', 'paviliun'))
            ->where('status', 'approved')
            ->where(function ($q) use ($today) {
                $q->whereDate('start', '<=', $today)
                    ->whereDate('end',   '>=', $today);
            })
            ->sum('ordered_unit');
        $availablePaviliun = $paviliunStock - $paviliunNotAvailable;

        // ====== EVENTS (opsional) ======
        $events = Transaction::where('status', 'approved')
            ->where(function ($q) use ($today) {
                $q->where(function ($q2) use ($today) {
                    $q2->where('start', '<=', $today)
                        ->where('end',   '>=', $today);
                });
            })
            ->orderBy('start', 'desc')
            ->take(10)
            ->get();

        // ====== RETURN VIEW (tanpa duplikat key) ======
        return view('admin.dashboard', [
            'charts'               => $charts,
            'types'                => $typesWanted,
            'periodLabel'          => $periodLabel,
            'useRange'             => $useRange,

            'year'                 => $year,
            'years'                => $years,
            'month'                => $month,
            'monthOptions'         => [
                0 => 'Semua Bulan',
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'Mei',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Agu',
                9 => 'Sep',
                10 => 'Okt',
                11 => 'Nov',
                12 => 'Des',
            ],

            'availableAula'        => $availableAula,
            'availableKelas'       => $availableKelas,
            'availableAsrama'      => $availableAsrama,
            'availablePaviliun'    => $availablePaviliun,
            'aulaNotAvailable'     => $aulaNotAvailable,
            'kelasNotAvailable'    => $kelasNotAvailable,
            'asramaNotAvailable'   => $asramaNotAvailable,
            'paviliunNotAvailable' => $paviliunNotAvailable,
            'aulaStock'            => $aulaStock,
            'kelasStock'           => $kelasStock,
            'asramaStock'          => $asramaStock,
            'paviliunStock'        => $paviliunStock,
            'events'               => $events,
            'items'                => $items,
        ]);
    }



    public function exportPerTahun(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        // Ambil semua properti
        $properties = Properties::select('id', 'name')
            ->orderBy('name')
            ->get();

        $months = range(1, 12);
        $report = [];

        foreach ($properties as $prop) {
            $row = [$prop->name]; // kolom pertama: Nama Fasilitas

            foreach ($months as $m) {
                $startOfMonth = Carbon::create($year, $m, 1)->startOfDay();
                $endOfMonth   = Carbon::create($year, $m, 1)->endOfMonth()->endOfDay();

                // Hitung total hari terpakai (sum overlap hari)
                $daysUsed = DB::table('transactions')
                    ->where('property_id', $prop->id)
                    ->where('status', 'approved')
                    ->whereDate('start', '<=', $endOfMonth)
                    ->whereDate('end', '>=', $startOfMonth)
                    ->selectRaw("
                    SUM(
                        CASE
                            WHEN LEAST(`end`, ?) < GREATEST(`start`, ?) THEN 0
                            ELSE DATEDIFF(LEAST(`end`, ?), GREATEST(`start`, ?)) + 1
                        END
                    ) AS total_days
                ", [$endOfMonth, $startOfMonth, $endOfMonth, $startOfMonth])
                    ->value('total_days') ?? 0;

                $row[] = (int) $daysUsed;
            }

            $report[] = $row;
        }

        return Excel::download(
            new PropertyUsageExport($report, $year),
            "Rekap_Penggunaan_{$year}.xlsx"
        );
    }
}
