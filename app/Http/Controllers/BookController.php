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
    public function index()
    {
        $books = Book::orderBy('title')->get();

        return view('books.index', compact('books'));
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
        // validasi input
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'publisher'   => 'required|string|max:255',
            'year'        => 'required|integer|min:1900|max:' . date('Y'),
            'stock'       => 'required|integer|min:0',

            // field baru
            'description' => 'nullable|string',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // upload cover kalau ada
        if ($request->hasFile('cover')) {
            // file akan disimpan di: storage/app/public/covers
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
        // validasi input
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'publisher'   => 'required|string|max:255',
            'year'        => 'required|integer|min:1900|max:' . date('Y'),
            'stock'       => 'required|integer|min:0',

            // field baru
            'description' => 'nullable|string',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // kalau ada cover baru, hapus cover lama lalu simpan yang baru
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
        // hapus file cover kalau ada
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
        // 1) Cek stok buku dulu
        if ($book->stock <= 0) {
            return back()
                ->withInput()
                ->with('error', 'Maaf, stok buku ini sedang habis.');
        }

        // 2) Validasi data siswa + tanggal pinjam
        $data = $request->validate([
            'student_name'  => 'required|string|max:255',
            'student_nis'   => 'required|string|max:50',
            'student_class' => 'required|string|max:50',
            // tanggal pinjam boleh dipilih maksimal 7 hari dari hari ini
            'borrow_date'   => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays(7)->toDateString(),
        ]);

        // 3) Batas maksimal 3 buku per siswa (status masih dipinjam/terlambat)
        $aktif = Borrowing::where('student_nis', $data['student_nis'])
            ->whereIn('status', ['Dipinjam', 'Terlambat'])
            ->count();

        if ($aktif >= 3) {
            return back()
                ->withInput()
                ->with('error', 'Peminjaman gagal. Batas maksimal 3 buku per siswa sudah tercapai.');
        }

        // 4) Hitung tanggal jatuh tempo otomatis (misal 7 hari dari tanggal pinjam)
        $borrowDate = \Carbon\Carbon::parse($data['borrow_date'])->toDateString();
        $dueDate    = \Carbon\Carbon::parse($data['borrow_date'])->addDays(7)->toDateString();

        // 5) Simpan ke tabel peminjaman
        Borrowing::create([
            'book_id'       => $book->id,
            'member_id'     => null, // karena siswa tidak login
            'borrow_date'   => $borrowDate,
            'due_date'      => $dueDate,
            'status'        => 'Dipinjam',

            // data siswa manual
            'student_name'  => $data['student_name'],
            'student_nis'   => $data['student_nis'],
            'student_class' => $data['student_class'],
        ]);

        // 6) Kurangi stok buku
        $book->decrement('stock');

        // 7) Notifikasi balik ke katalog
        return back()->with('success', 'Peminjaman berhasil dicatat. Silakan tunjukkan bukti ini kepada petugas perpustakaan.');
    }
}
