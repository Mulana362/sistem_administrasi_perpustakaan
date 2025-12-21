<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentBorrowController extends Controller
{
    /**
     * Simpan peminjaman dari form di katalog.
     * Route: POST /pinjam-buku  (name: student.borrow.store)
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $data = $request->validate([
            'book_id'       => 'required|exists:books,id',
            'student_name'  => 'required|string|max:255',
            'student_nis'   => 'required|string|max:50',
            'student_class' => 'required|string|max:50',
            'duration'      => 'required|integer|min:1|max:7',
        ]);

        // 2. Ambil buku yang dipilih
        $book = Book::findOrFail($data['book_id']);

        // Cek stok buku
        if ($book->stock <= 0) {
            return back()
                ->withInput()
                ->with('error', 'Stok buku sudah habis.');
        }

        // 3. Cari / buat anggota berdasarkan NIS
        $member = Member::firstOrCreate(
            ['nis' => $data['student_nis']], // kunci unik
            [
                'name'  => $data['student_name'],
                'class' => $data['student_class'],
            ]
        );

        // Kalau data nama / kelas berubah, update
        if ($member->name !== $data['student_name'] ||
            $member->class !== $data['student_class']) {

            $member->update([
                'name'  => $data['student_name'],
                'class' => $data['student_class'],
            ]);
        }

        // 4. Cek maksimal 3 buku aktif (return_date masih NULL)
        $activeCount = Borrowing::where('member_id', $member->id)
            ->whereNull('return_date')
            ->count();

        if ($activeCount >= 3) {
            return back()
                ->withInput()
                ->with('error', 'Maksimal 3 buku aktif per siswa. Kembalikan buku terlebih dahulu.');
        }

        // 5. Hitung tanggal pinjam & jatuh tempo
        $today    = Carbon::today();                      // tanggal pinjam = hari ini
        $duration = (int) $data['duration'];             // lama pinjam (hari)
        $dueDate  = $today->copy()->addDays($duration);  // tanggal jatuh tempo

        // 6. Simpan ke tabel borrowings
        Borrowing::create([
            'member_id'     => $member->id,
            'book_id'       => $book->id,
            'student_name'  => $data['student_name'],
            'student_nis'   => $data['student_nis'],
            'student_class' => $data['student_class'],
            'borrow_date'   => $today,
            'due_date'      => $dueDate,
            'return_date'   => null,
            'status'        => 'Dipinjam',
        ]);

        // 7. Kurangi stok buku
        $book->decrement('stock');

        // 8. Redirect kembali ke katalog dengan pesan sukses
        return redirect()
            ->route('catalog')
            ->with('success', 'Permintaan peminjaman berhasil disimpan.');
    }

    /**
     * Halaman cek status peminjaman siswa.
     * Route: GET /cek-status (name: student.borrow.status)
     * Param: ?nis=12345
     */
    public function status(Request $request)
    {
        $nis = $request->query('nis');

        $borrowings = collect();

        if ($nis) {
            $borrowings = Borrowing::where('student_nis', $nis)
                ->orderByDesc('borrow_date')
                ->with('book')
                ->get();
        }

        return view('student.borrow.status', [
            'nis'        => $nis,
            'borrowings' => $borrowings,
        ]);
    }

    /**
     * Kalau kamu pakai form POST untuk cek status,
     * bisa diarahkan ke method status di atas.
     * Route: POST /cek-status (name: student.borrow.check)
     */
    public function checkStatus(Request $request)
    {
        $nis = $request->input('nis');

        return redirect()->route('student.borrow.status', ['nis' => $nis]);
    }
}
