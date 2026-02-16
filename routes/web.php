<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\StudentBorrowController;
use App\Http\Controllers\ExcelImportController;

use App\Models\Book;
use App\Models\Member;

/*
|--------------------------------------------------------------------------
| HALAMAN DEPAN (PUBLIK)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');


/*
|--------------------------------------------------------------------------
| API (karena routes/api.php tidak ke-load)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {

    Route::get('/ping', function () {
        return response()->json([
            'status'  => 'ok',
            'message' => 'API berjalan normal'
        ]);
    });

    Route::get('/member/{nis}', function ($nis) {
        $nis = trim($nis);

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

});


/*
|--------------------------------------------------------------------------
| KATALOG BUKU – PUBLIK
|--------------------------------------------------------------------------
*/
Route::get('/katalog-buku', function () {
    $q = request('q');
    $booksQuery = Book::query();

    if ($q) {
        $booksQuery->where(function ($query) use ($q) {
            $query->where('title', 'like', "%{$q}%")
                ->orWhere('author', 'like', "%{$q}%")
                ->orWhere('publisher', 'like', "%{$q}%")
                ->orWhere('year', 'like', "%{$q}%");
        });
    }

    $books = $booksQuery->orderBy('title')->paginate(10);

    return view('student.books.catalog', compact('books', 'q'));
})->name('catalog');


/*
|--------------------------------------------------------------------------
| SISWA – PINJAM, CEK STATUS, KUNJUNGAN
|--------------------------------------------------------------------------
*/
Route::post('/pinjam-buku', [StudentBorrowController::class, 'store'])
    ->name('student.borrow.store');

Route::get('/cek-status', [StudentBorrowController::class, 'status'])
    ->name('student.borrow.status');

Route::post('/cek-status', [StudentBorrowController::class, 'checkStatus'])
    ->name('student.borrow.check');

/**
 * ✅ SISWA bisa perpanjang masa pengajuan (status: Diajukan)
 * Contoh URL: POST /pengajuan/12/perpanjang
 *
 * 🔥 FIX: method di controller kamu namanya extendRequest, bukan extend
 */
Route::post('/pengajuan/{borrowing}/perpanjang', [StudentBorrowController::class, 'extend'])
    ->name('student.borrow.extend');

Route::get('/kunjungan', function () {
    return view('student.visit.register');
})->name('visit.register');

Route::post('/kunjungan', [VisitorController::class, 'store'])
    ->name('visit.store');


/*
|--------------------------------------------------------------------------
| LOGIN / LOGOUT ADMIN
|--------------------------------------------------------------------------
*/
Route::get('login-admin', [AdminController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('login-admin', [AdminController::class, 'login'])
    ->name('admin.login.submit');

Route::get('logout-admin', [AdminController::class, 'logout'])
    ->name('admin.logout');


/*
|--------------------------------------------------------------------------
| DASHBOARD ADMIN
|--------------------------------------------------------------------------
*/
Route::get('admin/dashboard', [AdminController::class, 'dashboard'])
    ->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| 📂 PENGAJUAN KADALUARSA (SOFT DELETE)
|--------------------------------------------------------------------------
*/
Route::get('admin/borrowings/expired', [AdminController::class, 'expiredBorrowings'])
    ->name('admin.borrowings.expired');


/*
|--------------------------------------------------------------------------
| ACTION PENGAJUAN KADALUARSA
|--------------------------------------------------------------------------
*/
Route::post('admin/borrowings/expired/{id}/restore', [AdminController::class, 'restoreExpiredBorrowing'])
    ->name('admin.borrowings.expired.restore');

Route::delete('admin/borrowings/expired/{id}/force-delete', [AdminController::class, 'forceDeleteExpiredBorrowing'])
    ->name('admin.borrowings.expired.forceDelete');


/*
|--------------------------------------------------------------------------
| 🔥 TAMBAHAN: HAPUS SEMUA PENGAJUAN KADALUARSA (CEPAT)
|--------------------------------------------------------------------------
*/
Route::delete('admin/borrowings/expired/force-all', [AdminController::class, 'forceDeleteAllExpired'])
    ->name('admin.borrowings.expired.forceAll');


/*
|--------------------------------------------------------------------------
| IMPORT DATA EXCEL
|--------------------------------------------------------------------------
*/
Route::get('admin/import', [ExcelImportController::class, 'index'])
    ->name('admin.import.index');

Route::get('admin/import/books', [ExcelImportController::class, 'index'])
    ->name('books.import.form');

Route::post('admin/import/books', [ExcelImportController::class, 'importBooks'])
    ->name('admin.import.books');

Route::post('admin/import/members', [ExcelImportController::class, 'importMembers'])
    ->name('admin.import.members');

/**
 * ⚠️ Pastikan method destroyLog ADA di ExcelImportController
 */
Route::delete('admin/import/logs/{log}', [ExcelImportController::class, 'destroyLog'])
    ->name('admin.import.logs.destroy');


/*
|--------------------------------------------------------------------------
| CETAK KARTU ANGGOTA
|--------------------------------------------------------------------------
*/
Route::get('members/{member}/cetak-kartu', [MemberController::class, 'cetakKartu'])
    ->name('members.cetak.kartu');


/*
|--------------------------------------------------------------------------
| REKAP KUNJUNGAN
|--------------------------------------------------------------------------
*/
Route::get('visitors/export', [VisitorController::class, 'exportExcel'])
    ->name('visitors.export');

Route::get('visitors/print', [VisitorController::class, 'print'])
    ->name('visitors.print');


/*
|--------------------------------------------------------------------------
| CRUD MASTER DATA
|--------------------------------------------------------------------------
*/
Route::resource('members', MemberController::class);
Route::resource('books', BookController::class);
Route::resource('borrowings', BorrowingController::class);
Route::resource('visitors', VisitorController::class);
