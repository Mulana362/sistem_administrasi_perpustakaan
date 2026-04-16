<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Tampilkan daftar buku (halaman admin).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->q);

        $books = Book::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('book_code', 'like', "%{$q}%")
                        ->orWhere('title', 'like', "%{$q}%")
                        ->orWhere('author', 'like', "%{$q}%")
                        ->orWhere('publisher', 'like', "%{$q}%")
                        ->orWhere('year', 'like', "%{$q}%")
                        ->orWhere('no_rak', 'like', "%{$q}%");
                });
            })
            ->orderBy('title')
            ->get();

        return view('books.index', compact('books', 'q'));
    }

    /**
     * Form tambah buku.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Simpan buku baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'book_code'    => 'required|string|max:100|unique:books,book_code',
            'title'        => 'required|string|max:255',
            'author'       => 'required|string|max:255',
            'publisher'    => 'required|string|max:255',
            'year'         => 'required|integer|min:1900|max:' . date('Y'),
            'no_rak'       => 'nullable|string|max:50',
            'stock'        => 'required|integer|min:0',
            'description'  => 'nullable|string',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Book::create($data);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Form edit buku.
     */
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Update data buku.
     */
    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'book_code'    => 'required|string|max:100|unique:books,book_code,' . $book->id,
            'title'        => 'required|string|max:255',
            'author'       => 'required|string|max:255',
            'publisher'    => 'required|string|max:255',
            'year'         => 'required|integer|min:1900|max:' . date('Y'),
            'no_rak'       => 'nullable|string|max:50',
            'stock'        => 'required|integer|min:0',
            'description'  => 'nullable|string',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }

            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Hapus buku.
     */
    public function destroy(Book $book)
    {
        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }

    /**
     * PINJAM DARI KATALOG (SISWA TANPA LOGIN)
     * Route: POST /katalog-buku/{book}/pinjam  -> name: student.books.borrow
     */
    public function borrowFromCatalog(Request $request, Book $book)
    {
        if ($book->stock <= 0) {
            return back()
                ->withInput()
                ->with('error', 'Maaf, stok buku ini sedang habis.');
        }

        $data = $request->validate([
            'student_name'  => 'required|string|max:255',
            'student_nis'   => 'required|string|max:50',
            'student_class' => 'required|string|max:50',
            'borrow_date'   => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays(7)->toDateString(),
        ]);

        $aktif = Borrowing::where('student_nis', $data['student_nis'])
            ->whereIn('status', ['Dipinjam', 'Terlambat'])
            ->count();

        if ($aktif >= 3) {
            return back()
                ->withInput()
                ->with('error', 'Peminjaman gagal. Batas maksimal 3 buku per siswa sudah tercapai.');
        }

        $borrowDate = \Carbon\Carbon::parse($data['borrow_date'])->toDateString();
        $dueDate    = \Carbon\Carbon::parse($data['borrow_date'])->addDays(7)->toDateString();

        Borrowing::create([
            'book_id'       => $book->id,
            'member_id'     => null,
            'borrow_date'   => $borrowDate,
            'due_date'      => $dueDate,
            'status'        => 'Dipinjam',
            'student_name'  => $data['student_name'],
            'student_nis'   => $data['student_nis'],
            'student_class' => $data['student_class'],
        ]);

        $book->decrement('stock');

        return back()->with('success', 'Peminjaman berhasil dicatat. Silakan tunjukkan bukti ini kepada petugas perpustakaan.');
    }
}
