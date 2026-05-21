<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Mendukung multi-role dengan separator pipe (|), contoh:
     *   middleware('checkRole:admin|supervisor')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Satu atau lebih role dipisah '|' (tanpa spasi)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Guard: pastikan user sudah login sebelum akses property role-nya
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // Pisahkan multi-role (misal: "admin|supervisor") dan bersihkan spasi
        $allowedRoles = array_map('trim', explode('|', $role));

        // Cek apakah role user saat ini termasuk dalam daftar yang diizinkan
        if (! in_array(auth()->user()->role, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
