<?php

use Illuminate\Support\Facades\Route;
use App\Models\Member;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route di file ini otomatis prefix "/api"
| dan pakai middleware "api" (stateless).
|--------------------------------------------------------------------------
*/

Route::get('/ping', function () {
    return response()->json([
        'status'  => 'ok',
        'message' => 'API berjalan normal'
    ]);
});

/*
|--------------------------------------------------------------------------
| Cari data anggota berdasarkan NIS
|--------------------------------------------------------------------------
| GET /api/member/{nis}
|--------------------------------------------------------------------------
*/
Route::get('/member/{nis}', function (string $nis) {

    // ✅ bersihin input (spasi, enter, dll)
    $nis = trim($nis);

    // ✅ kalau kosong, langsung balikin error
    if ($nis === '') {
        return response()->json([
            'not_found' => true,
            'message'   => 'NIS kosong'
        ], 400);
    }

    // ✅ query aman (cocok untuk nis string / integer)
    $member = Member::where('nis', (string) $nis)->first();

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
