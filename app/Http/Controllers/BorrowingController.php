<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    // LIST DATA PEMINJAMAN
    public function index()
    {
        $borrowings = Borrowing::with('book')
            ->orderBy('borrow_date', 'desc')
            ->get();

        return view('borrowings.index', compact('borrowings'));
    }

    // FORM TAMBAH PEMINJAMAN
    public function create()
    {
        // Ambil hanya buku yang stoknya masih ada
        $books = Book::where('stock', '>', 0)
            ->orderBy('title')
            ->get();

        return view('borrowings.create', compact('books'));
    }

    // SIMPAN PEMINJAMAN BARU
    public function store(Request $request)
    {
        $request->validate([
            // member_id opsional (buat admin kalau suatu saat mau pilih member)
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

            // Lock row buku biar stok aman (anti double pinjam bareng-bareng)
            $book = Book::where('id', $request->book_id)->lockForUpdate()->firstOrFail();

            if ($book->stock < 1) {
                abort(422, 'Stok buku "' . $book->title . '" sudah habis.');
            }

            Borrowing::create([
                'member_id'     => $request->member_id ?? null, // ✅ penting: opsional
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

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Peminjaman baru berhasil disimpan dan stok buku dikurangi 1.');
    }

    // FORM EDIT
    public function edit(Borrowing $borrowing)
    {
        $books = Book::orderBy('title')->get();
        return view('borrowings.edit', compact('borrowing', 'books'));
    }

    // UPDATE PEMINJAMAN
    public function update(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'member_id'     => 'nullable|exists:members,id',

            'student_name'  => 'required|string|max:255',
            'student_nis'   => 'required|string|max:50',
            'student_class' => 'required|string|max:50',
            'book_id'       => 'required|exists:books,id',
            'borrow_date'   => 'required|date',
            'due_date'      => 'required|date|after_or_equal:borrow_date',
            'status'        => 'required|in:Dipinjam,Kembali,Terlambat',
        ]);

        DB::transaction(function () use ($request, $borrowing) {

            $oldStatus = $borrowing->status;
            $newStatus = $request->status;
            $oldBookId = $borrowing->book_id;
            $newBookId = (int) $request->book_id;

            // update field dasar
            $borrowing->member_id     = $request->member_id ?? null; // ✅ opsional
            $borrowing->student_name  = $request->student_name;
            $borrowing->student_nis   = $request->student_nis;
            $borrowing->student_class = $request->student_class;
            $borrowing->book_id       = $newBookId;
            $borrowing->borrow_date   = $request->borrow_date;
            $borrowing->due_date      = $request->due_date;
            $borrowing->status        = $newStatus;

            // Kalau dari Dipinjam -> (Kembali/Terlambat): set return_date + stok balik
            if ($oldStatus === 'Dipinjam' && $newStatus !== 'Dipinjam') {
                $borrowing->return_date = now()->toDateString();

                $oldBook = Book::where('id', $oldBookId)->lockForUpdate()->first();
                if ($oldBook) {
                    $oldBook->increment('stock');
                }
            }

            $borrowing->save();
        });

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Data peminjaman berhasil diupdate.');
    }

    // HAPUS PEMINJAMAN
    public function destroy(Borrowing $borrowing)
    {
        DB::transaction(function () use ($borrowing) {
            // kalau masih Dipinjam dan dihapus → anggap buku kembali ke rak
            if ($borrowing->status === 'Dipinjam') {
                $book = Book::where('id', $borrowing->book_id)->lockForUpdate()->first();
                if ($book) {
                    $book->increment('stock');
                }
            }

            $borrowing->delete();
        });

        return redirect()
            ->route('borrowings.index')
            ->with('success', 'Peminjaman berhasil dihapus dan stok buku dikembalikan.');
    }
}
