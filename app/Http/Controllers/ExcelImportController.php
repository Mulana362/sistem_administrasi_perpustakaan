<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\ExcelImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportController extends Controller
{
    /**
     * Halaman form import + riwayat import.
     * route: GET admin/import  atau  GET admin/import/books
     */
    public function index()
    {
        if (!Schema::hasTable('excel_import_logs')) {
            $logs = collect();
        } else {
            $logs = ExcelImportLog::orderByDesc('imported_at')
                ->take(10)
                ->get();
        }

        return view('admin.import.index', compact('logs'));
    }

    /**
     * Import data BUKU dari Excel.
     * Form: input name="file_books"
     * Route: POST admin/import/books (name: admin.import.books)
     *
     * Format kolom Excel (A sampai I):
     * A: No (opsional, tidak dipakai)
     * B: ID BUKU (book_code) contoh BK-001
     * C: COVER (nama file di storage/covers atau URL)
     * D: JUDUL
     * E: DESKRIPSI
     * F: PENGARANG
     * G: PENERBIT
     * H: TAHUN
     * I: STOCK
     */
    public function importBooks(Request $request)
    {
        $request->validate([
            'file_books' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file_books');

        // Simpan file Excel untuk arsip
        $storedPath = $file->store('imports/books', 'public');

        // Load Excel
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true); // index A,B,C,...

        $createdIds = [];

        DB::beginTransaction();

        try {
            $firstRow = true;

            foreach ($rows as $row) {
                // skip header
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                // ✅ Ambil data sesuai kolom Excel kamu (A sampai I)
                $no          = trim($row['A'] ?? ''); // No (ga dipakai)
                $bookCode    = trim($row['B'] ?? ''); // ID BUKU (BK-001)
                $coverName   = trim($row['C'] ?? ''); // COVER
                $title       = trim($row['D'] ?? ''); // JUDUL
                $description = trim($row['E'] ?? ''); // DESKRIPSI
                $author      = trim($row['F'] ?? ''); // PENGARANG
                $publisher   = trim($row['G'] ?? ''); // PENERBIT
                $yearRaw     = trim($row['H'] ?? ''); // TAHUN
                $stockRaw    = trim($row['I'] ?? ''); // STOCK

                // Skip baris kosong
                if ($title === '' && $author === '' && $publisher === '' && $bookCode === '') {
                    continue;
                }

                // Konversi tahun dan stok (kasih default biar gak error)
                $year  = is_numeric($yearRaw) ? (int) $yearRaw : 0;  // ✅ default 0 biar year gak null
                $stock = is_numeric($stockRaw) ? (int) $stockRaw : 0;

                // ====== HANDLE COVER ======
                $coverPath = null;

                if ($coverName !== '') {
                    // 1) kalau URL -> download otomatis
                    if (filter_var($coverName, FILTER_VALIDATE_URL)) {
                        try {
                            $imageContent = @file_get_contents($coverName);
                            if ($imageContent !== false) {
                                $ext = 'jpg';
                                $lower = strtolower($coverName);

                                if (str_contains($lower, '.png'))  $ext = 'png';
                                if (str_contains($lower, '.jpeg')) $ext = 'jpeg';
                                if (str_contains($lower, '.webp')) $ext = 'webp';

                                $fileName = uniqid('cover_') . '.' . $ext;
                                $fullPath = 'covers/' . $fileName;

                                Storage::disk('public')->put($fullPath, $imageContent);
                                $coverPath = $fullPath;
                            }
                        } catch (\Throwable $e) {
                            $coverPath = null;
                        }
                    } else {
                        // 2) kalau nama file lokal -> cek storage public/covers/
                        $guessPath = 'covers/' . $coverName;
                        if (Storage::disk('public')->exists($guessPath)) {
                            $coverPath = $guessPath;
                        }
                    }
                }
                // ====== END HANDLE COVER ======

                // ✅ wajib punya judul biar data rapi
                if ($title === '') {
                    continue;
                }

                // ✅ updateOrCreate berdasarkan book_code (BK-001)
                // kalau book_code kosong, fallback create biasa
                if ($bookCode !== '') {
                    $book = Book::updateOrCreate(
                        ['book_code' => $bookCode],
                        [
                            'book_code'   => $bookCode,
                            'title'       => $title,
                            'description' => $description ?: null,
                            'author'      => $author ?: null,
                            'publisher'   => $publisher ?: null,
                            'year'        => $year,   // ✅ aman: gak null
                            'stock'       => $stock,
                            'cover'       => $coverPath,
                        ]
                    );
                } else {
                    $book = Book::create([
                        'book_code'   => null,
                        'title'       => $title,
                        'description' => $description ?: null,
                        'author'      => $author ?: null,
                        'publisher'   => $publisher ?: null,
                        'year'        => $year,   // ✅ aman: gak null
                        'stock'       => $stock,
                        'cover'       => $coverPath,
                    ]);
                }

                $createdIds[] = $book->id;
            }

            // Simpan log import (kalau tabel ada)
            if (Schema::hasTable('excel_import_logs')) {
                ExcelImportLog::create([
                    'type'          => 'books',
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => $storedPath,
                    'created_count' => count($createdIds),
                    'created_ids'   => json_encode($createdIds),
                    'imported_at'   => now(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'Import data buku berhasil. Jumlah data: ' . count($createdIds));
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal import data buku: ' . $e->getMessage());
        }
    }

    /**
     * Import data ANGGOTA / SISWA.
     * Form: input name="file_members"
     * Route: POST admin/import/members (name: admin.import.members)
     */
    public function importMembers(Request $request)
    {
        $request->validate([
            'file_members' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file       = $request->file('file_members');
        $storedPath = $file->store('imports/members', 'public');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        $createdIds = [];

        DB::beginTransaction();

        try {
            $firstRow = true;

            foreach ($rows as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                // A: NIS, B: Nama, C: Kelas, D: Jenis Kelamin, E: No HP, F: Alamat
                $nis    = trim($row['A'] ?? '');
                $name   = trim($row['B'] ?? '');
                $class  = trim($row['C'] ?? '');
                $gender = trim($row['D'] ?? '');
                $phone  = trim($row['E'] ?? '');
                $addr   = trim($row['F'] ?? '');

                if ($nis === '' || $name === '') {
                    continue;
                }

                $member = Member::updateOrCreate(
                    ['nis' => $nis],
                    [
                        'name'    => $name,
                        'class'   => $class ?: null,
                        'gender'  => $gender ?: null,
                        'phone'   => $phone ?: null,
                        'address' => $addr ?: null,
                    ]
                );

                $createdIds[] = $member->id;
            }

            if (Schema::hasTable('excel_import_logs')) {
                ExcelImportLog::create([
                    'type'          => 'members',
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => $storedPath,
                    'created_count' => count($createdIds),
                    'created_ids'   => json_encode($createdIds),
                    'imported_at'   => now(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'Import data anggota berhasil. Jumlah data: ' . count($createdIds));
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal import data anggota: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Hapus satu batch import log + data yg dibuat (opsional).
     * Route: DELETE admin/import/logs/{log}
     * name : admin.import.logs.destroy
     */
    public function destroyLog(ExcelImportLog $log)
    {
        DB::beginTransaction();

        try {
            // Ambil daftar ID yg dibuat dari import ini
            $ids = json_decode($log->created_ids ?? '[]', true);

            if (!is_array($ids)) {
                $ids = [];
            }

            // Hapus data yang dibuat dari batch import ini
            if ($log->type === 'books') {
                if (count($ids) > 0) {
                    Book::whereIn('id', $ids)->delete();
                }
            } elseif ($log->type === 'members') {
                if (count($ids) > 0) {
                    Member::whereIn('id', $ids)->delete();
                }
            }

            // Hapus file excel arsip
            if ($log->file_path && Storage::disk('public')->exists($log->file_path)) {
                Storage::disk('public')->delete($log->file_path);
            }

            // Hapus lognya
            $log->delete();

            DB::commit();

            return back()->with('success', 'Batch import berhasil dihapus (data + file + log).');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus batch import: ' . $e->getMessage());
        }
    }
}
