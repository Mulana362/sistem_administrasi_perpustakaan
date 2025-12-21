<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($request->session()->has('student_id')) {
            return redirect()->route('student.dashboard');
        }

        if ($request->session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
