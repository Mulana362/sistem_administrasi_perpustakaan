<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing;

class AdminController extends Controller
{
    // =========================
    // LOGIN ADMIN
    // =========================
    public function showLoginForm()
    {
        return view('admin.login-admin');
    }

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

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // =========================
    // DASHBOARD
    // =========================
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // =========================
    // 🔥 PENGAJUAN KADALUARSA
    // =========================

    /**
     * Halaman data pengajuan kadaluarsa (soft deleted)
     * URL: /admin/borrowings/expired
     */
    public function expiredBorrowings()
    {
        $expired = Borrowing::onlyTrashed()
            ->with(['book', 'member'])
            ->where('status', 'Diajukan')
            ->orderByDesc('deleted_at')
            ->paginate(10);

        return view('borrowings.expired', compact('expired'));
    }

    /**
     * Restore pengajuan kadaluarsa
     */
    public function restoreExpiredBorrowing($id)
    {
        $borrowing = Borrowing::onlyTrashed()->findOrFail($id);
        $borrowing->restore();

        return back()->with('success', 'Pengajuan berhasil dipulihkan.');
    }

    /**
     * Hapus permanen pengajuan kadaluarsa (1 data)
     */
    public function forceDeleteExpiredBorrowing($id)
    {
        $borrowing = Borrowing::onlyTrashed()->findOrFail($id);
        $borrowing->forceDelete();

        return back()->with('success', 'Pengajuan kadaluarsa berhasil dihapus permanen.');
    }

    /**
     * ✅ HAPUS SEMUA pengajuan kadaluarsa sekaligus (biar cepat)
     * hanya yang status = Diajukan dan sudah soft delete (onlyTrashed)
     */
    public function forceDeleteAllExpiredBorrowings()
    {
        $deleted = Borrowing::onlyTrashed()
            ->where('status', 'Diajukan')
            ->forceDelete();

        return back()->with('success', "Semua pengajuan kadaluarsa berhasil dihapus permanen. Total: {$deleted}");
    }
}
