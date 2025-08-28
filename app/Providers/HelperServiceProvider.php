<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class HelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Helper untuk format rentang tanggal konsisten lokal
        if (!function_exists('tanggalRangeID')) {
            function tanggalRangeID($start, $end) {
                $s = Carbon::parse($start);
                $e = Carbon::parse($end);
                
                if ($s->isSameDay($e)) {
                    return $s->translatedFormat('d M Y');
                }
                
                if ($s->isSameMonth($e) && $s->isSameYear($e)) {
                    return $s->translatedFormat('d').'–'.$e->translatedFormat('d M Y');
                }
                
                return $s->translatedFormat('d M Y').' — '.$e->translatedFormat('d M Y');
            }
        }
    }
}
