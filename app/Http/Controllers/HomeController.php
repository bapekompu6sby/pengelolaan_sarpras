<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Kalau belum login: tampilkan landing page/welcome
        if (!auth()->check()) {
            // Blade:
            return redirect()->route('dashboard');

            // Inertia (jika pakai Breeze + Inertia):
            // return inertia('Welcome', [
            //     'canLogin' => Route::has('login'),
            //     'canRegister' => Route::has('register'),
            // ]);
        }

        $user = $request->user();

        // === Dengan Spatie Permission ===
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin')) {
                return redirect()->route('dashboardAdmin');
            }
            if ($user->hasRole('supervisor')) {
                return redirect()->route('dashboardAdmin'); // ganti sesuai route-mu
            }

            // user biasa
            return redirect()->route('dashboard'); // ganti sesuai route user
        }

        // === Tanpa Spatie (kolom users.role) ===
        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboardAdmin');
            case 'supervisor':
                return redirect()->route('dashboardAdmin'); // ganti sesuai route-mu
            default:
                return redirect()->route('dashboard');
        }
    }
public function internalBapekomp()
{
    $today = now('Asia/Jakarta')->toDateString();

    $data = Transaction::with(['properties', 'user'])
        ->where('status', 'approved')
        // tampilkan yang masih berlangsung/akan datang
        ->whereDate('end', '>=', $today)
        ->orderBy('start', 'asc')
        ->get();

    return view('internal_bapekom', compact('data'));
}

}
