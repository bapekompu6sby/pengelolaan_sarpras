<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use App\Models\Wisma;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $events = Transaction::where('start', '>=', now(-10))
            ->where('end', '<=', now()->addDays(30))
            ->orderBy('start', 'asc')
            ->get();

        $paviliunTotal = 14;
        $paviliun = Wisma::where('room', 'like', '%pav%')
                ->where('isOut', 0)
                ->whereRaw('? BETWEEN start AND end', [$today])
                ->count();

        $pavAntrianQuery = Wisma::where('room', 'like', '%pav%')
                            ->where('isOut', 0)
                            ->count();
        // dd($pavAntrianQuery->toSql());

        $paviliunAvailable = $paviliunTotal - $paviliun;
        $paviliunQueue = $pavAntrianQuery - $paviliun;

        $wismaTotal = 206;
        $wisma = Wisma::where('room', 'not like', '%pav%')
                ->Where('isOut', 0)
                ->whereRaw('? BETWEEN start AND end', [$today])
                ->count();
        $wisAntrianQuery = Wisma::where('room', 'not like', '%pav%')
                            ->where('isOut', 0)
                            ->count();
    
        $wismaAvailable = $wismaTotal - $wisma;
        $wismaQueue = $wisAntrianQuery - $wisma;

        return view('welcome', [
            'events' => $events,
            'paviliun' => $paviliun,
            'paviliunAvailable' => $paviliunAvailable,
            'paviliunQueue' => $paviliunQueue,
            'wisma' => $wisma,
            'wismaAvailable' => $wismaAvailable,
            'wismaQueue' => $wismaQueue,
        ]);
    }
}
