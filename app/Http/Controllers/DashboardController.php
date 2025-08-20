<?php

namespace App\Http\Controllers;

use App\Models\Wisma;
use App\Models\Properties;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        //aula
        $aulaStock = Properties::where('type', 'aula')->sum('unit');

        $aulaNotAvailable = Transaction::whereHas('properties', function ($query) {
            $query->where('type', 'aula');
        })

            ->where('status', 'approved')
            ->where('start', '<=', $today, 'and', 'end', '>=', $today)
            ->sum('ordered_unit');


        $availableAula = $aulaStock - $aulaNotAvailable;



        //kelas
        $kelasStock = Properties::where('type', 'kelas')->sum('unit');

        $kelasNotAvailable = Transaction::whereHas('properties', function ($query) {
            $query->where('type', 'kelas');
        })
            ->where('status', 'approved')
            ->where('start', '<=', $today, 'and', 'end', '>=', $today)
            ->sum('ordered_unit');

        $availableKelas = $kelasStock - $kelasNotAvailable;



        //asrama
        $asramaStock = Properties::where('type', 'asrama')->sum('unit');

        $asramaNotAvailable = Transaction::whereHas('properties', function ($query) {
            $query->where('type', 'asrama');
        })
            ->where('status', 'approved')
            ->where('start', '<=', $today, 'and', 'end', '>=', $today)
            ->sum('ordered_unit');

        $availableAsrama = $asramaStock - $asramaNotAvailable;



        //paviliun
        $paviliunStock = Properties::where('type', 'paviliun')->sum('unit');

        $paviliunNotAvailable = Transaction::whereHas('properties', function ($query) {
            $query->where('type', 'paviliun');
        })
            ->where('status', 'approved')
            ->where('start', '<=', $today, 'and', 'end', '>=', $today)
            ->sum('ordered_unit');

        $availablePaviliun = $paviliunStock - $paviliunNotAvailable;



        $events = Transaction::where('status', 'approved')->get();
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







        // $paviliunTotal = 14;
        // $paviliun = Wisma::where('room', 'like', '%pav%')
        //     ->where('isOut', 0)
        //     ->whereRaw('? BETWEEN start AND end', [$today])
        //     ->count();

        // $pavAntrianQuery = Wisma::where('room', 'like', '%pav%')
        //     ->where('isOut', 0)
        //     ->count();
        // // dd($pavAntrianQuery->toSql());

        // $paviliunAvailable = $paviliunTotal - $paviliun;
        // $paviliunQueue = $pavAntrianQuery - $paviliun;

        // $wismaTotal = 206;
        // $wisma = Wisma::where('room', 'not like', '%pav%')
        //     ->Where('isOut', 0)
        //     ->whereRaw('? BETWEEN start AND end', [$today])
        //     ->count();
        // $wisAntrianQuery = Wisma::where('room', 'not like', '%pav%')
        //     ->where('isOut', 0)
        //     ->count();

        // $wismaAvailable = $wismaTotal - $wisma;
        // $wismaQueue = $wisAntrianQuery - $wisma;

        // return view('welcome', [
        //     'events' => $events,
        //     'paviliun' => $paviliun,
        //     'paviliunAvailable' => $paviliunAvailable,
        //     'paviliunQueue' => $paviliunQueue,
        //     'wisma' => $wisma,
        //     'wismaAvailable' => $wismaAvailable,
        //     'wismaQueue' => $wismaQueue,
        // ]);
    }
}
