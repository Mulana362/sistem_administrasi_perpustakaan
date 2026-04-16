<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\ExcelImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportController extends Controller
{
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

    public function importBooks(Request $request)
    {
        $request->validate([
            'file_books' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file_books');
        $storedPath = $file->store('imports/books', 'public');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $createdIds = [];

        DB::beginTransaction();

        try {
            $firstRow = true;

            foreach ($rows as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                $bookCode    = trim($row['B'] ?? '');
                $coverName   = trim($row['C'] ?? '');
                $title       = trim($row['D'] ?? '');
                $description = trim($row['E'] ?? '');
                $author      = trim($row['F'] ?? '');
                $publisher   = trim($row['G'] ?? '');
                $yearRaw     = trim($row['H'] ?? '');
                $stockRaw    = trim($row['I'] ?? '');
                $noRak       = trim($row['J'] ?? '');

                if ($title === '' && $author === '' && $publisher === '' && $bookCode === '') {
                    continue;
                }

                $year = is_numeric($yearRaw) ? (int) $yearRaw : 0;
                $stock = is_numeric($stockRaw) ? (int) $stockRaw : 0;

                $coverPath = null;

                if ($coverName !== '') {
                    if (filter_var($coverName, FILTER_VALIDATE_URL)) {
                        try {
                            $imageContent = @file_get_contents($coverName);

                            if ($imageContent !== false) {
                                $ext = 'jpg';
                                $lower = strtolower($coverName);

                                if (str_contains($lower, '.png')) {
                                    $ext = 'png';
                                }
                                if (str_contains($lower, '.jpeg')) {
                                    $ext = 'jpeg';
                                }
                                if (str_contains($lower, '.webp')) {
                                    $ext = 'webp';
                                }

                                $fileName = uniqid('cover_') . '.' . $ext;
                                $fullPath = 'covers/' . $fileName;

                                Storage::disk('public')->put($fullPath, $imageContent);
                                $coverPath = $fullPath;
                            }
                        } catch (\Throwable $e) {
                            $coverPath = null;
                        }
                    } else {
                        $guessPath = 'covers/' . $coverName;

                        if (Storage::disk('public')->exists($guessPath)) {
                            $coverPath = $guessPath;
                        }
                    }
                }

                if ($title === '') {
                    continue;
                }

                if ($bookCode !== '') {
                    $book = Book::updateOrCreate(
                        ['book_code' => $bookCode],
                        [
                            'book_code'   => $bookCode,
                            'title'       => $title,
                            'description' => $description ?: null,
                            'author'      => $author ?: null,
                            'publisher'   => $publisher ?: null,
                            'year'        => $year,
                            'stock'       => $stock,
                            'no_rak'      => $noRak !== '' ? $noRak : null,
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
                        'year'        => $year,
                        'stock'       => $stock,
                        'no_rak'      => $noRak !== '' ? $noRak : null,
                        'cover'       => $coverPath,
                    ]);
                }

                $createdIds[] = $book->id;
            }

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

    public function importMembers(Request $request)
    {
        $request->validate([
            'file_members' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file_members');
        $storedPath = $file->store('imports/members', 'public');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $createdIds = [];

        DB::beginTransaction();

        try {
            $firstRow = true;

            foreach ($rows as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

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

    public function destroyLog(ExcelImportLog $log)
    {
        DB::beginTransaction();

        try {
            $ids = json_decode($log->created_ids ?? '[]', true);

            if (!is_array($ids)) {
                $ids = [];
            }

            if ($log->type === 'books') {
                if (count($ids) > 0) {
                    Borrowing::whereIn('book_id', $ids)->delete();
                    Book::whereIn('id', $ids)->delete();
                }
            } elseif ($log->type === 'members') {
                if (count($ids) > 0) {
                    Borrowing::whereIn('member_id', $ids)->delete();
                    Member::whereIn('id', $ids)->delete();
                }
            }

            if ($log->file_path && Storage::disk('public')->exists($log->file_path)) {
                Storage::disk('public')->delete($log->file_path);
            }

            $log->delete();

            DB::commit();

            return back()->with('success', 'Batch import berhasil dihapus. Data terkait ikut dibersihkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal hapus batch import: ' . $e->getMessage());
        }
    }
}
