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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if ($role == 'admin' && auth()->user()->role != 'admin' ) {
            return redirect()->back();
        }
        if ($role == 'user' && auth()->user()->role != 'user' ) {
            return redirect()->back();
        }
        if ($role == 'guest' && auth()->user()->role != 'guest' ) {
            abort(403);
        }
        return $next($request);
    }
    // public function handle(Request $request, Closure $next, string $role)
    // {
    //     if ($request->user()->role !== $role) {
    //         return redirect('/login');
    //     }
    //     return $next($request);
    // }
}
