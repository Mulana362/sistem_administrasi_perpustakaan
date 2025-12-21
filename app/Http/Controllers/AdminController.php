<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Halaman login admin
    public function showLoginForm()
    {
        return view('admin.login-admin');
    }

    // Proses login admin
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // cek role admin
            if (Auth::user()->role !== 'admin') {
                Auth::logout();

                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Akses ditolak. Akun ini bukan akun admin.');
            }

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Email atau password tidak sesuai.');
    }

    // Logout admin
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // Dashboard admin
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
