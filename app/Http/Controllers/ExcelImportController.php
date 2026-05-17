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
                ->take(30)
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

                if ($book->wasRecentlyCreated) {
                    $createdIds[] = $book->id;
                }
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

                if ($member->wasRecentlyCreated) {
                    $createdIds[] = $member->id;
                }
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

            $deletedCount = 0;
            $keptCount = 0;

            if ($log->type === 'books') {
                if (count($ids) > 0) {
                    $protectedIds = $this->getProtectedBookIds($ids);
                    $deletableIds = array_values(array_diff($ids, $protectedIds));

                    if (count($deletableIds) > 0) {
                        $deletedCount = Book::whereIn('id', $deletableIds)->delete();
                    }

                    $keptCount = count($ids) - count($deletableIds);
                }
            } elseif ($log->type === 'members') {
                if (count($ids) > 0) {
                    $protectedIds = $this->getProtectedMemberIds($ids);
                    $deletableIds = array_values(array_diff($ids, $protectedIds));

                    if (count($deletableIds) > 0) {
                        $deletedCount = Member::whereIn('id', $deletableIds)->delete();
                    }

                    $keptCount = count($ids) - count($deletableIds);
                }
            }

            if ($log->file_path && Storage::disk('public')->exists($log->file_path)) {
                Storage::disk('public')->delete($log->file_path);
            }

            $log->delete();

            DB::commit();

            $message = 'Batch import berhasil dihapus. Data baru yang aman dihapus: ' . $deletedCount . '.';

            if ($keptCount > 0) {
                $message .= ' Ada ' . $keptCount . ' data yang tidak dihapus karena masih punya riwayat transaksi/laporan.';
            }

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal hapus batch import: ' . $e->getMessage());
        }
    }
    public function backupDatabase()
    {
        try {
            $tables = $this->getBackupTables();

            $backupData = [
                'app' => config('app.name'),
                'created_at' => now()->toDateTimeString(),
                'tables' => [],
            ];

            foreach ($tables as $table) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                $backupData['tables'][$table] = DB::table($table)->get()->map(function ($row) {
                    return (array) $row;
                })->values()->toArray();
            }

            $backupDir = storage_path('app/backups');

            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $fileName = 'backup_perpustakaan_' . now()->format('Ymd_His') . '.json';
            $filePath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

            file_put_contents($filePath, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->download($filePath);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    public function restoreDatabase(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        DB::beginTransaction();

        try {
            $content = file_get_contents($request->file('backup_file')->getRealPath());
            $backupData = json_decode($content, true);

            if (!is_array($backupData) || !isset($backupData['tables']) || !is_array($backupData['tables'])) {
                throw new \Exception('Format file backup tidak valid.');
            }

            $allowedTables = $this->getBackupTables();

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($allowedTables as $table) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                if (!array_key_exists($table, $backupData['tables'])) {
                    continue;
                }

                DB::table($table)->truncate();

                $rows = $backupData['tables'][$table];

                if (is_array($rows) && count($rows) > 0) {
                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            DB::commit();

            return back()->with('success', 'Restore database berhasil. Data sistem sudah dikembalikan dari file backup.');
        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::rollBack();

            return back()->with('error', 'Gagal restore database: ' . $e->getMessage());
        }
    }

    private function getBackupTables(): array
    {
        return [
            'books',
            'members',
            'borrowings',
            'visitors',
            'book_issues',
            'excel_import_logs',
        ];
    }

    private function getProtectedBookIds(array $ids): array
    {
        $protectedIds = [];

        if (Schema::hasTable('borrowings') && Schema::hasColumn('borrowings', 'book_id')) {
            $protectedIds = array_merge(
                $protectedIds,
                Borrowing::whereIn('book_id', $ids)
                    ->pluck('book_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray()
            );
        }

        if (Schema::hasTable('book_issues') && Schema::hasColumn('book_issues', 'book_id')) {
            $protectedIds = array_merge(
                $protectedIds,
                DB::table('book_issues')
                    ->whereIn('book_id', $ids)
                    ->pluck('book_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray()
            );
        }

        return array_values(array_unique($protectedIds));
    }

    private function getProtectedMemberIds(array $ids): array
    {
        $protectedIds = [];

        if (Schema::hasTable('borrowings') && Schema::hasColumn('borrowings', 'member_id')) {
            $protectedIds = array_merge(
                $protectedIds,
                Borrowing::whereIn('member_id', $ids)
                    ->pluck('member_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray()
            );
        }

        if (Schema::hasTable('visitors') && Schema::hasColumn('visitors', 'member_id')) {
            $protectedIds = array_merge(
                $protectedIds,
                DB::table('visitors')
                    ->whereIn('member_id', $ids)
                    ->pluck('member_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray()
            );
        }

        return array_values(array_unique($protectedIds));
    }

}
