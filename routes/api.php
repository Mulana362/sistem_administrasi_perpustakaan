<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Member;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route dalam file ini otomatis diberi prefix "/api"
| dan berjalan tanpa session (stateless).
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 1️⃣ API CARI DATA ANGGOTA BERDASARKAN NIS
|--------------------------------------------------------------------------
| Dipakai untuk fitur Auto-Fill:
| - Form peminjaman buku
| - Form kunjungan
| - Cetak kartu anggota
|
| Contoh request:
| GET /api/member/1001
|--------------------------------------------------------------------------
*/
Route::get('/member/{nis}', function ($nis) {

    // cari anggota berdasarkan kolom "nis"
    $member = Member::where('nis', $nis)->first();

    if (!$member) {
        return response()->json([
            'not_found' => true,
            'message'   => 'Data anggota tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'not_found' => false,
        'name'      => $member->name,
        'nis'       => $member->nis,
        'class'     => $member->class,
        'gender'    => $member->gender ?? null,
        'phone'     => $member->phone ?? null,
    ]);
});


/*
|--------------------------------------------------------------------------
| 2️⃣ TEST API (optional)
|--------------------------------------------------------------------------
*/

Route::get('/ping', function () {
    return ['status' => 'ok', 'message' => 'API berjalan normal'];
});
