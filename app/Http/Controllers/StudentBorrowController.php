<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class StudentBorrowController extends Controller
{
    /**
     * SISWA AJUKAN PINJAM (status: Diajukan)
     * expired_at otomatis +2 hari dari sekarang
     */
    public function store(Request $request)
    {
        // kompatibel: terima field lama & baru
        $request->merge([
            'nis'   => $request->nis   ?? $request->student_nis,
            'name'  => $request->name  ?? $request->student_name,
            'class' => $request->class ?? $request->student_class,
            'days'  => $request->days  ?? $request->duration,
        ]);

        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'nis'     => 'required|string|max:50',
            'name'    => 'required|string|max:255',
            'class'   => 'required|string|max:50',
            'days'    => 'required|integer|min:1|max:7',
        ]);

        $book = Book::findOrFail($data['book_id']);

        if ($book->stock <= 0) {
            return back()->withInput()->with('error', 'Stok buku sudah habis.');
        }

        $nis = trim((string) $data['nis']);
        $member = Member::where('nis', $nis)->first();

        if (!$member) {
            return back()->withInput()->with('error', 'NIS tidak terdaftar. Hubungi admin.');
        }

        // Maksimal 3 buku aktif (Diajukan + Dipinjam + Terlambat, Kembali tidak dihitung)
        // FIX: hitung berdasarkan student_nis agar data lama / input admin tetap ikut terhitung
        $activeCount = Borrowing::where('student_nis', $member->nis)
            ->whereIn('status', ['Diajukan', 'Dipinjam', 'Terlambat'])
            ->whereNull('return_date')
            ->count();

        if ($activeCount >= 3) {
            return back()->withInput()->with('error', 'Maksimal 3 buku aktif. Kembalikan buku dulu.');
        }

        // cegah dobel ajukan buku yang sama
        // FIX: cek berdasarkan student_nis agar konsisten dengan data riwayat siswa
        $already = Borrowing::where('student_nis', $member->nis)
            ->where('book_id', $book->id)
            ->whereIn('status', ['Diajukan', 'Dipinjam', 'Terlambat'])
            ->whereNull('return_date')
            ->exists();

        if ($already) {
            return back()->withInput()->with('error', 'Buku ini sudah diajukan / sedang dipinjam.');
        }

        $expire = now()->addDays(2);

        $payload = [
            'member_id'     => $member->id,
            'book_id'       => $book->id,

            'student_name'  => $member->name,
            'student_nis'   => $member->nis,
            'student_class' => $member->class,

            'borrow_date'   => null,
            'due_date'      => null,
            'return_date'   => null,
            'duration'      => (int) $data['days'],
            'status'        => 'Diajukan',

            'expired_at'       => $expire,
            'extend_count'     => 0,
            'last_extended_at' => null,
        ];

        if (Schema::hasColumn('borrowings', 'expires_at')) {
            $payload['expires_at'] = $expire;
        }

        Borrowing::create($payload);

        return redirect()
            ->route('catalog')
            ->with('success', 'Pengajuan berhasil dikirim. Tunggu admin memproses.');
    }

    /**
     * Halaman cek status peminjaman/pengajuan berdasarkan NIS
     */
    public function status(Request $request)
    {
        $nis = $request->query('nis');
        $borrowings = collect();

        if ($nis) {
            // otomatis ubah Dipinjam yang sudah lewat jatuh tempo jadi Terlambat
            Borrowing::where('student_nis', $nis)
                ->where('status', 'Dipinjam')
                ->whereNotNull('due_date')
                ->whereNull('return_date')
                ->whereDate('due_date', '<', today())
                ->update(['status' => 'Terlambat']);

            $borrowings = Borrowing::where('student_nis', $nis)
                ->with('book')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('student.borrow.status', [
            'nis'        => $nis,
            'borrowings' => $borrowings,
        ]);
    }

    public function checkStatus(Request $request)
    {
        $nis = $request->input('nis');
        return redirect()->route('student.borrow.status', ['nis' => $nis]);
    }

    /**
     * Alias kalau route kamu manggil extendRequest
     */
    public function extendRequest(Request $request, Borrowing $borrowing)
    {
        return $this->extend($request, $borrowing);
    }

    /**
     * SISWA PERPANJANG PENGAJUAN / PEMINJAMAN (MAX 2x, +2 hari)
     * Route: POST /pengajuan/{borrowing}/perpanjang
     */
    public function extend(Request $request, Borrowing $borrowing)
    {
        $data = $request->validate([
            'nis' => 'required|string|max:50',
        ]);

        // keamanan: NIS harus sama dengan pemilik data
        if (trim((string) $data['nis']) !== trim((string) $borrowing->student_nis)) {
            return back()->with('error', 'Akses ditolak. Ini bukan data milik kamu.');
        }

        // kalau sudah lewat jatuh tempo, otomatis ubah jadi Terlambat
        if ($borrowing->status === 'Dipinjam' && !empty($borrowing->due_date)) {
            $dueDateCheck = Carbon::parse($borrowing->due_date);

            if (now()->gt($dueDateCheck->copy()->endOfDay())) {
                $borrowing->status = 'Terlambat';
                $borrowing->save();

                return back()->with('error', 'Peminjaman sudah terlambat dan tidak bisa diperpanjang.');
            }
        }

        // hanya Diajukan / Dipinjam yang boleh diperpanjang
        if (!in_array($borrowing->status, ['Diajukan', 'Dipinjam'], true)) {
            return back()->with('error', 'Hanya status Diajukan atau Dipinjam yang bisa diperpanjang.');
        }

        $extendCount = (int) ($borrowing->extend_count ?? 0);

        if ($extendCount >= 2) {
            return back()->with('error', 'Batas perpanjangan sudah maksimal (2x).');
        }

        if ($borrowing->status === 'Diajukan') {
            $rawExpire = $borrowing->expired_at ?? $borrowing->expires_at ?? null;
            $expireDate = $rawExpire ? Carbon::parse($rawExpire) : now();

            // masih berlaku sampai akhir hari
            if (now()->gt($expireDate->copy()->endOfDay())) {
                return back()->with('error', 'Pengajuan sudah kadaluarsa. Silakan ajukan ulang.');
            }

            $newExpire = $expireDate->copy()->addDays(2);

            $borrowing->expired_at = $newExpire;

            if (array_key_exists('expires_at', $borrowing->getAttributes())) {
                $borrowing->expires_at = $newExpire;
            }

            $borrowing->extend_count = $extendCount + 1;
            $borrowing->last_extended_at = now();
            $borrowing->save();

            return back()->with('success', 'Pengajuan berhasil diperpanjang (+2 hari).');
        }

        if ($borrowing->status === 'Dipinjam') {
            if (!$borrowing->due_date) {
                return back()->with('error', 'Tanggal jatuh tempo tidak tersedia.');
            }

            $dueDate = Carbon::parse($borrowing->due_date);

            // masih berlaku sampai akhir hari jatuh tempo
            if (now()->gt($dueDate->copy()->endOfDay())) {
                $borrowing->status = 'Terlambat';
                $borrowing->save();

                return back()->with('error', 'Peminjaman sudah terlambat dan tidak bisa diperpanjang.');
            }

            $borrowing->due_date = $dueDate->copy()->addDays(2);
            $borrowing->extend_count = $extendCount + 1;
            $borrowing->last_extended_at = now();
            $borrowing->save();

            return back()->with('success', 'Peminjaman berhasil diperpanjang (+2 hari). Jatuh tempo diperbarui.');
        }

        return back()->with('error', 'Perpanjangan tidak dapat diproses.');
    }
}
