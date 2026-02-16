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

        // ✅ Maksimal 3 buku aktif (Diajukan + Dipinjam)
        $activeCount = Borrowing::where('member_id', $member->id)
            ->whereIn('status', ['Diajukan', 'Dipinjam'])
            ->whereNull('return_date')
            ->count();

        if ($activeCount >= 3) {
            return back()->withInput()->with('error', 'Maksimal 3 buku aktif. Kembalikan buku dulu.');
        }

        // cegah dobel ajukan buku yang sama (Diajukan / Dipinjam)
        $already = Borrowing::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['Diajukan', 'Dipinjam'])
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

            // ✅ masa pengajuan 2 hari
            'expired_at'    => $expire,

            // ✅ max perpanjang 2x
            'extend_count'     => 0,
            'last_extended_at' => null,
        ];

        // ✅ kalau tabel kamu juga punya kolom expires_at, isi juga biar konsisten
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
     * ✅ Alias kalau route kamu manggil extendRequest (biar aman tanpa ubah web.php)
     */
    public function extendRequest(Request $request, Borrowing $borrowing)
    {
        return $this->extend($request, $borrowing);
    }

    /**
     * ✅ SISWA PERPANJANG PENGAJUAN (MAX 2x)
     * Route: POST /pengajuan/{borrowing}/perpanjang
     */
    public function extend(Request $request, Borrowing $borrowing)
    {
        $data = $request->validate([
            'nis' => 'required|string|max:50',
        ]);

        // keamanan: NIS harus sama dengan pemilik pengajuan
        if (trim((string) $data['nis']) !== trim((string) $borrowing->student_nis)) {
            return back()->with('error', 'Akses ditolak. Ini bukan pengajuan milik kamu.');
        }

        // hanya boleh extend kalau masih Diajukan
        if ($borrowing->status !== 'Diajukan') {
            return back()->with('error', 'Hanya pengajuan status Diajukan yang bisa diperpanjang.');
        }

        // ✅ ambil tanggal kadaluarsa dari expired_at / expires_at (mana yang ada)
        $rawExpire = $borrowing->expired_at ?? $borrowing->expires_at ?? null;

        // kalau tidak ada sama sekali, anggap dari sekarang
        $expireDate = $rawExpire ? Carbon::parse($rawExpire) : now();

        // kalau sudah expired
        if ($expireDate->isPast()) {
            return back()->with('error', 'Pengajuan sudah kadaluarsa. Silakan ajukan ulang.');
        }

        $extendCount = (int) ($borrowing->extend_count ?? 0);

        // max 2x
        if ($extendCount >= 2) {
            return back()->with('error', 'Batas perpanjang pengajuan sudah maksimal (2x).');
        }

        // ✅ tambah 2 hari dari expire yang sekarang
        $newExpire = $expireDate->copy()->addDays(2);

        // ✅ update expired_at
        $borrowing->expired_at = $newExpire;

        // ✅ kalau kolom expires_at ada, update juga
        if (array_key_exists('expires_at', $borrowing->getAttributes())) {
            $borrowing->expires_at = $newExpire;
        }

        $borrowing->extend_count = $extendCount + 1;
        $borrowing->last_extended_at = now();
        $borrowing->save();

        return back()->with('success', 'Pengajuan berhasil diperpanjang (+2 hari).');
    }
}
