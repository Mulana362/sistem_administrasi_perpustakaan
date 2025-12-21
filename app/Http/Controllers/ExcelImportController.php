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
        // Kalau tabel log belum ada (misal DB baru), kirim koleksi kosong
        if (! Schema::hasTable('excel_import_logs')) {
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
     * Route: POST admin/import/books   (name: admin.import.books)
     */
    public function importBooks(Request $request)
    {
        $request->validate([
            'file_books' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file_books');

        // Simpan dulu file Excel ke storage (untuk arsip)
        $storedPath = $file->store('imports/books', 'public');

        // Load Excel
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true); // pakai index A,B,C,...

        $createdIds = [];

        DB::beginTransaction();

        try {
            $firstRow = true;

            foreach ($rows as $row) {
                // Lewati baris header (baris pertama)
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                // Ambil data sesuai kolom Excel kamu
                $coverName   = trim($row['B'] ?? ''); // COVER
                $title       = trim($row['C'] ?? ''); // JUDUL
                $description = trim($row['D'] ?? ''); // DESKRIPSI
                $author      = trim($row['E'] ?? ''); // PENGARANG
                $publisher   = trim($row['F'] ?? ''); // PENERBIT
                $yearRaw     = trim($row['G'] ?? ''); // TAHUN
                $stockRaw    = trim($row['H'] ?? ''); // STOCK

                // Skip baris kosong
                if ($title === '' && $author === '' && $publisher === '') {
                    continue;
                }

                // Konversi tahun dan stok ke integer (atau null)
                $year = is_numeric($yearRaw) ? (int) $yearRaw : null;
                $stock = is_numeric($stockRaw) ? (int) $stockRaw : 0;

                // ====== HANDLE COVER ======
                $coverPath = null;

                if ($coverName !== '') {
                    // 1) Kalau isinya URL gambar -> download otomatis
                    if (filter_var($coverName, FILTER_VALIDATE_URL)) {
                        try {
                            $imageContent = @file_get_contents($coverName);

                            if ($imageContent !== false) {
                                // generate nama file dari judul + uniqid
                                $ext = 'jpg';
                                if (str_contains($coverName, '.png')) {
                                    $ext = 'png';
                                } elseif (str_contains($coverName, '.jpeg')) {
                                    $ext = 'jpeg';
                                }

                                $fileName  = uniqid('cover_') . '.' . $ext;
                                $fullPath  = 'covers/' . $fileName;

                                Storage::disk('public')->put($fullPath, $imageContent);

                                $coverPath = $fullPath;
                            }
                        } catch (\Throwable $e) {
                            // kalau gagal download, biarkan cover null
                        }
                    } else {
                        // 2) Kalau hanya nama file -> cek di storage/app/public/covers
                        $guessPath = 'covers/' . $coverName;

                        if (Storage::disk('public')->exists($guessPath)) {
                            $coverPath = $guessPath;
                        }
                    }
                }
                // ====== END HANDLE COVER ======

                $book = Book::create([
                    'title'       => $title,
                    'description' => $description ?: null,
                    'author'      => $author ?: null,
                    'publisher'   => $publisher ?: null,
                    'year'        => $year,
                    'stock'       => $stock,
                    'cover'       => $coverPath, // null kalau tidak ada
                ]);

                $createdIds[] = $book->id;
            }

            // Simpan log import
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
     * Route: POST admin/import/members   (name: admin.import.members)
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

                // Contoh struktur:
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

            return back()->with('success', 'Import data anggota / siswa berhasil. Jumlah data: ' . count($createdIds));
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal import data anggota: ' . $e->getMessage());
        }
    }

    /**
     * Hapus satu batch import (opsional).
     * Route: DELETE admin/import/logs/{log}
     */
    public function destroyLog(ExcelImportLog $log)
    {
        DB::beginTransaction();

        try {
            // Hapus data yang dibuat dari import ini
            $ids = json_decode($log->created_ids ?? '[]', true);

            if ($log->type === 'books') {
                Book::whereIn('id', $ids)->delete();
            } elseif ($log->type === 'members') {
                Member::whereIn('id', $ids)->delete();
            }

            // Hapus file excel yang tersimpan
            if ($log->file_path && Storage::disk('public')->exists($log->file_path)) {
                Storage::disk('public')->delete($log->file_path);
            }

            $log->delete();

            DB::commit();

            return back()->with('success', 'Batch import & data terkait berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus batch: ' . $e->getMessage());
        }
    }
}
