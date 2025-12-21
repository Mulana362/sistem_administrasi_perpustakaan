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
| FORM PINJAM, CEK STATUS, KUNJUNGAN (SISWA)
|--------------------------------------------------------------------------
*/
Route::post('/pinjam-buku', [StudentBorrowController::class, 'store'])
    ->name('student.borrow.store');

Route::get('/cek-status', [StudentBorrowController::class, 'status'])
    ->name('student.borrow.status');

Route::post('/cek-status', [StudentBorrowController::class, 'checkStatus'])
    ->name('student.borrow.check');

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
| IMPORT DATA EXCEL (BUKU & ANGGOTA)
|--------------------------------------------------------------------------
*/
Route::get('admin/import', [ExcelImportController::class, 'index'])
    ->name('admin.import.index');

// Tombol "📥 Import Excel" di halaman buku
Route::get('admin/import/books', [ExcelImportController::class, 'index'])
    ->name('books.import.form');

// proses import
Route::post('admin/import/books', [ExcelImportController::class, 'importBooks'])
    ->name('admin.import.books');

Route::post('admin/import/members', [ExcelImportController::class, 'importMembers'])
    ->name('admin.import.members');

// 🔴 route untuk tombol "Hapus Batch" (RIWAYAT IMPORT)
Route::delete('admin/import/logs/{log}', [ExcelImportController::class, 'destroyLog'])
    ->name('admin.import.logs.destroy');

/*
|--------------------------------------------------------------------------
| CETAK KARTU ANGGOTA (harus di atas resource members)
|--------------------------------------------------------------------------
*/
Route::get('members/{member}/cetak-kartu', [MemberController::class, 'cetakKartu'])
    ->name('members.cetak.kartu');

/*
|--------------------------------------------------------------------------
| CRUD MASTER DATA
|--------------------------------------------------------------------------
*/
Route::resource('members', MemberController::class);
Route::resource('books', BookController::class);
Route::resource('borrowings', BorrowingController::class);
Route::resource('visitors', VisitorController::class);

/*
|--------------------------------------------------------------------------
| REKAP KUNJUNGAN
|--------------------------------------------------------------------------
*/
Route::get('visitors/export', [VisitorController::class, 'exportExcel'])
    ->name('visitors.export');

Route::get('visitors/print', [VisitorController::class, 'print'])
    ->name('visitors.print');
