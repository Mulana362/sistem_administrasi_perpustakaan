<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!$request->session()->has('student_id') && !$request->session()->has('admin_id')) {
            return redirect()->route('student.login'); // default redirect ke login siswa
        }

        return $next($request);
    }
}
