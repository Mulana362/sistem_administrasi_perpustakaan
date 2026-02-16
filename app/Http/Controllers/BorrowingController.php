<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function index()
    {
        // ✅ AUTO HAPUS pengajuan kadaluarsa (pakai expired_at)
        Borrowing::where('status', 'Diajukan')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->delete();

        // ✅ Statistik (jelas)
        $countPengajuan = Borrowing::where('status', 'Diajukan')->count();
        $countAktif     = Borrowing::whereIn('status', ['Dipinjam','Terlambat'])->count();
        $countKembali   = Borrowing::where('status', 'Kembali')->count();
        $countTerlambat = Borrowing::where('status', 'Dipinjam')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();

        // ✅ Data per tab (pagination terpisah biar gak campur)
        $pengajuan = Borrowing::with(['book','member'])
            ->where('status', 'Diajukan')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'pengajuan_page');

        $aktif = Borrowing::with(['book','member'])
            ->whereIn('status', ['Dipinjam','Terlambat'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'aktif_page');

        $riwayat = Borrowing::with(['book','member'])
            ->where('status', 'Kembali')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'riwayat_page');

        return view('borrowings.index', compact(
            'countPengajuan',
            'countAktif',
            'countKembali',
            'countTerlambat',
            'pengajuan',
            'aktif',
            'riwayat'
        ));
    }

    public function create()
    {
        $books = Book::where('stock', '>', 0)->orderBy('title')->get();
        return view('borrowings.create', compact('books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id'     => 'nullable|exists:members,id',
            'student_name'  => 'required|string|max:255',
            'student_nis'   => 'required|string|max:50',
            'student_class' => 'required|string|max:50',
            'book_id'       => 'required|exists:books,id',
            'borrow_date'   => 'required|date',
            'due_date'      => 'required|date|after_or_equal:borrow_date',
            'duration'      => 'required|integer|min:1|max:7',
        ]);

        DB::transaction(function () use ($request) {
            $book = Book::where('id', $request->book_id)->lockForUpdate()->firstOrFail();

            if ($book->stock < 1) abort(422, 'Stok buku "' . $book->title . '" sudah habis.');

            Borrowing::create([
                'member_id'     => $request->member_id ?? null,
                'student_name'  => $request->student_name,
                'student_nis'   => $request->student_nis,
                'student_class' => $request->student_class,
                'book_id'       => $request->book_id,
                'borrow_date'   => $request->borrow_date,
                'due_date'      => $request->due_date,
                'return_date'   => null,
                'duration'      => $request->duration,
                'status'        => 'Dipinjam',
            ]);

            $book->decrement('stock');
        });

        return redirect()->route('borrowings.index')
            ->with('success', 'Peminjaman baru berhasil disimpan dan stok buku dikurangi 1.');
    }

    public function edit(Borrowing $borrowing)
    {
        $books = Book::orderBy('title')->get();
        return view('borrowings.edit', compact('borrowing', 'books'));
    }

    public function update(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'member_id'     => 'nullable|exists:members,id',
            'student_name'  => 'required|string|max:255',
            'student_nis'   => 'required|string|max:50',
            'student_class' => 'required|string|max:50',
            'book_id'       => 'required|exists:books,id',
            'borrow_date'   => 'nullable|date',
            'due_date'      => 'nullable|date|after_or_equal:borrow_date',
            'duration'      => 'nullable|integer|min:1|max:7',
            'status'        => 'required|in:Diajukan,Dipinjam,Kembali,Terlambat',
        ]);

        DB::transaction(function () use ($request, $borrowing) {
            $oldStatus = $borrowing->status;
            $newStatus = $request->status;

            $oldBookId = (int) $borrowing->book_id;
            $newBookId = (int) $request->book_id;

            $oldBook = Book::where('id', $oldBookId)->lockForUpdate()->first();
            $newBook = Book::where('id', $newBookId)->lockForUpdate()->first();

            if ($oldBookId !== $newBookId && $oldStatus === 'Dipinjam') {
                if ($oldBook) $oldBook->increment('stock');
                if (!$newBook || $newBook->stock < 1) abort(422, 'Stok buku baru sudah habis.');
                $newBook->decrement('stock');
            }

            // Approve: Diajukan -> Dipinjam
            if ($oldStatus === 'Diajukan' && $newStatus === 'Dipinjam') {
                if (!$newBook || $newBook->stock < 1) abort(422, 'Stok buku sudah habis. Tidak bisa approve.');

                $newBook->decrement('stock');

                $duration = (int)($request->duration ?? $borrowing->duration ?? 1);
                $now = Carbon::now();

                $borrowing->borrow_date = $now->toDateString();
                $borrowing->due_date    = $now->copy()->addDays($duration)->toDateString();
                $borrowing->return_date = null;

                // pengajuan udah diproses => expired_at boleh dikosongin
                $borrowing->expired_at = null;
            }

            // Dipinjam -> Diajukan
            if ($oldStatus === 'Dipinjam' && $newStatus === 'Diajukan') {
                if ($oldBook) $oldBook->increment('stock');

                $borrowing->borrow_date = null;
                $borrowing->due_date    = null;
                $borrowing->return_date = null;

                // reset expired_at
                $borrowing->expired_at = now()->addDays(2);
            }

            // Dipinjam -> Kembali/Terlambat
            if ($oldStatus === 'Dipinjam' && in_array($newStatus, ['Kembali', 'Terlambat'], true)) {
                $borrowing->return_date = now()->toDateString();
                if ($oldBook) $oldBook->increment('stock');
            }

            if ($newStatus === 'Diajukan') {
                $borrowing->borrow_date = null;
                $borrowing->due_date    = null;
                $borrowing->return_date = null;

                if (!$borrowing->expired_at) {
                    $borrowing->expired_at = now()->addDays(2);
                }
            }

            if ($newStatus === 'Dipinjam') {
                $borrowing->return_date = null;
                if ($request->filled('borrow_date')) $borrowing->borrow_date = $request->borrow_date;
                if ($request->filled('due_date'))    $borrowing->due_date    = $request->due_date;
            }

            if ($newStatus === 'Dipinjam' && (empty($borrowing->borrow_date) || empty($borrowing->due_date))) {
                $duration = (int)($request->duration ?? $borrowing->duration ?? 1);
                $now = Carbon::now();
                $borrowing->borrow_date = $borrowing->borrow_date ?: $now->toDateString();
                $borrowing->due_date    = $borrowing->due_date ?: $now->copy()->addDays($duration)->toDateString();
            }

            $borrowing->member_id     = $request->member_id ?? null;
            $borrowing->student_name  = $request->student_name;
            $borrowing->student_nis   = $request->student_nis;
            $borrowing->student_class = $request->student_class;
            $borrowing->book_id       = $newBookId;

            if ($request->filled('duration')) $borrowing->duration = (int) $request->duration;

            $borrowing->status = $newStatus;
            $borrowing->save();
        });

        return redirect()->route('borrowings.index')->with('success', 'Data peminjaman berhasil diupdate.');
    }

    public function destroy(Borrowing $borrowing)
    {
        DB::transaction(function () use ($borrowing) {
            if ($borrowing->status === 'Dipinjam') {
                $book = Book::where('id', $borrowing->book_id)->lockForUpdate()->first();
                if ($book) $book->increment('stock');
            }
            $borrowing->delete();
        });

        return redirect()->route('borrowings.index')->with('success', 'Data berhasil dihapus.');
    }
}
