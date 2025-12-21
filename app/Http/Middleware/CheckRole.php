<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Ambil role dari session, misal: 'admin' atau 'student'
        $userRole = session('role');

        if ($userRole !== $role) {
            // kalau role tidak cocok, redirect ke halaman utama
            return redirect('/');
        }

        return $next($request);
    }
}
