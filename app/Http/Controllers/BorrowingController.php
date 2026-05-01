<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('active_tab', 'pengajuan');

        // AUTO UBAH status Dipinjam yang sudah lewat jatuh tempo menjadi Terlambat + hitung denda
        Borrowing::where('status', 'Dipinjam')
            ->whereNotNull('due_date')
            ->whereNull('return_date')
            ->whereDate('due_date', '<', today())
            ->get()
            ->each(function ($item) {
                $lateDays = Carbon::parse($item->due_date)->diffInDays(today());

                $item->status = 'Terlambat';
                $item->late_days = $lateDays;
                $item->fine_amount = $lateDays * 2000;

                if (is_null($item->fine_paid)) {
                    $item->fine_paid = false;
                }

                $item->save();
            });

        // Sinkronisasi denda untuk data yang memang sudah berstatus Terlambat
        Borrowing::where('status', 'Terlambat')
            ->whereNotNull('due_date')
            ->whereNull('return_date')
            ->get()
            ->each(function ($item) {
                $lateDays = Carbon::parse($item->due_date)->diffInDays(today());

                $item->late_days = $lateDays;
                $item->fine_amount = $lateDays * 2000;

                if (is_null($item->fine_paid)) {
                    $item->fine_paid = false;
                }

                $item->save();
            });

        // AUTO ISI return_date yang kosong untuk data lama status Kembali
        Borrowing::where('status', 'Kembali')
            ->whereNull('return_date')
            ->get()
            ->each(function ($item) {
                $item->return_date = $item->due_date ?? $item->borrow_date ?? now()->toDateString();

                $lateDays = 0;
                if (!empty($item->due_date) && Carbon::parse($item->return_date)->gt(Carbon::parse($item->due_date))) {
                    $lateDays = Carbon::parse($item->due_date)->diffInDays(Carbon::parse($item->return_date));
                }

                $item->late_days = $lateDays;
                $item->fine_amount = $lateDays * 2000;

                if (is_null($item->fine_paid)) {
                    $item->fine_paid = false;
                }

                $item->save();
            });

        // Hapus pengajuan kadaluarsa
        Borrowing::where('status', 'Diajukan')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->delete();

        $countPengajuan = Borrowing::where('status', 'Diajukan')->count();

        $countAktif = Borrowing::where('status', 'Dipinjam')
            ->whereNull('return_date')
            ->count();

        $countKembali = Borrowing::where('status', 'Kembali')->count();

        $countTerlambat = Borrowing::where('status', 'Terlambat')
            ->whereNull('return_date')
            ->count();

        $pengajuan = Borrowing::with(['book', 'member'])
            ->where('status', 'Diajukan')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'pengajuan_page')
            ->appends($request->query());

        $aktif = Borrowing::with(['book', 'member'])
            ->where('status', 'Dipinjam')
            ->whereNull('return_date')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'aktif_page')
            ->appends($request->query());

        $riwayat = Borrowing::with(['book', 'member'])
            ->where('status', 'Kembali')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'riwayat_page')
            ->appends($request->query());

        return view('borrowings.index', compact(
            'countPengajuan',
            'countAktif',
            'countKembali',
            'countTerlambat',
            'pengajuan',
            'aktif',
            'riwayat',
            'activeTab'
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

            if ($book->stock < 1) {
                throw ValidationException::withMessages([
                    'book_id' => 'Stok buku "' . $book->title . '" sudah habis.',
                ]);
            }

            $activeCount = Borrowing::where('student_nis', $request->student_nis)
                ->whereIn('status', ['Diajukan', 'Dipinjam', 'Terlambat'])
                ->whereNull('return_date')
                ->count();

            if ($activeCount >= 3) {
                throw ValidationException::withMessages([
                    'student_nis' => 'Siswa ini sudah mencapai maksimal 3 buku aktif.',
                ]);
            }

            $already = Borrowing::where('student_nis', $request->student_nis)
                ->where('book_id', $request->book_id)
                ->whereIn('status', ['Diajukan', 'Dipinjam', 'Terlambat'])
                ->whereNull('return_date')
                ->exists();

            if ($already) {
                throw ValidationException::withMessages([
                    'book_id' => 'Buku ini sudah diajukan / sedang dipinjam oleh siswa tersebut.',
                ]);
            }

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
                'late_days'     => 0,
                'fine_amount'   => 0,
                'fine_paid'     => false,
            ]);

            $book->decrement('stock');
        });

        $tab = $request->input('active_tab', 'aktif');

        return redirect()->route('borrowings.index', ['active_tab' => $tab])
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
            'fine_paid'     => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $borrowing) {
            $oldStatus = $borrowing->status;
            $newStatus = $request->status;

            $oldBookId = (int) $borrowing->book_id;
            $newBookId = (int) $request->book_id;
            $newStudentNis = trim((string) $request->student_nis);

            $oldBook = Book::where('id', $oldBookId)->lockForUpdate()->first();
            $newBook = Book::where('id', $newBookId)->lockForUpdate()->first();

            if (in_array($newStatus, ['Diajukan', 'Dipinjam', 'Terlambat'], true)) {
                $activeCount = Borrowing::where('student_nis', $newStudentNis)
                    ->whereIn('status', ['Diajukan', 'Dipinjam', 'Terlambat'])
                    ->whereNull('return_date')
                    ->where('id', '!=', $borrowing->id)
                    ->count();

                if ($activeCount >= 3) {
                    throw ValidationException::withMessages([
                        'student_nis' => 'Siswa ini sudah mencapai maksimal 3 buku aktif.',
                    ]);
                }

                $already = Borrowing::where('student_nis', $newStudentNis)
                    ->where('book_id', $newBookId)
                    ->whereIn('status', ['Diajukan', 'Dipinjam', 'Terlambat'])
                    ->whereNull('return_date')
                    ->where('id', '!=', $borrowing->id)
                    ->exists();

                if ($already) {
                    throw ValidationException::withMessages([
                        'book_id' => 'Buku ini sudah diajukan / sedang dipinjam oleh siswa tersebut.',
                    ]);
                }
            }

            if ($oldBookId !== $newBookId && in_array($oldStatus, ['Dipinjam', 'Terlambat'], true)) {
                if ($oldBook) {
                    $oldBook->increment('stock');
                }

                if (!$newBook || $newBook->stock < 1) {
                    throw ValidationException::withMessages([
                        'book_id' => 'Stok buku baru sudah habis.',
                    ]);
                }

                $newBook->decrement('stock');
            }

            if ($oldStatus === 'Diajukan' && $newStatus === 'Dipinjam') {
                if (!$newBook || $newBook->stock < 1) {
                    throw ValidationException::withMessages([
                        'book_id' => 'Stok buku sudah habis. Tidak bisa approve.',
                    ]);
                }

                $newBook->decrement('stock');

                $duration = (int) ($request->duration ?? $borrowing->duration ?? 1);
                $now = Carbon::now();

                $borrowing->borrow_date = $now->toDateString();
                $borrowing->due_date    = $now->copy()->addDays($duration)->toDateString();
                $borrowing->return_date = null;
                $borrowing->expired_at  = null;
                $borrowing->late_days   = 0;
                $borrowing->fine_amount = 0;
                $borrowing->fine_paid   = false;
            }

            if ($oldStatus === 'Dipinjam' && $newStatus === 'Diajukan') {
                if ($oldBook) {
                    $oldBook->increment('stock');
                }

                $borrowing->borrow_date = null;
                $borrowing->due_date    = null;
                $borrowing->return_date = null;
                $borrowing->expired_at  = now()->addDays(2);
                $borrowing->late_days   = 0;
                $borrowing->fine_amount = 0;
                $borrowing->fine_paid   = false;
            }

            if ($oldStatus === 'Dipinjam' && $newStatus === 'Kembali') {
                $borrowing->return_date = now()->toDateString();

                $lateDays = 0;
                if (!empty($borrowing->due_date) && Carbon::parse($borrowing->return_date)->gt(Carbon::parse($borrowing->due_date))) {
                    $lateDays = Carbon::parse($borrowing->due_date)->diffInDays(Carbon::parse($borrowing->return_date));
                }

                $borrowing->late_days = $lateDays;
                $borrowing->fine_amount = $lateDays * 2000;

                if ($oldBook) {
                    $oldBook->increment('stock');
                }
            }

            if ($oldStatus === 'Dipinjam' && $newStatus === 'Terlambat') {
                $borrowing->return_date = null;

                $lateDays = 0;
                if (!empty($borrowing->due_date)) {
                    $lateDays = Carbon::parse($borrowing->due_date)->diffInDays(today());
                }

                $borrowing->late_days = $lateDays;
                $borrowing->fine_amount = $lateDays * 2000;
            }

            if ($oldStatus === 'Terlambat' && $newStatus === 'Kembali') {
                $borrowing->return_date = now()->toDateString();

                $lateDays = 0;
                if (!empty($borrowing->due_date)) {
                    $lateDays = Carbon::parse($borrowing->due_date)->diffInDays(Carbon::parse($borrowing->return_date));
                }

                $borrowing->late_days = $lateDays;
                $borrowing->fine_amount = $lateDays * 2000;

                if ($oldBook) {
                    $oldBook->increment('stock');
                }
            }

            if ($newStatus === 'Diajukan') {
                $borrowing->borrow_date = null;
                $borrowing->due_date    = null;
                $borrowing->return_date = null;

                if (!$borrowing->expired_at) {
                    $borrowing->expired_at = now()->addDays(2);
                }

                $borrowing->late_days   = 0;
                $borrowing->fine_amount = 0;
                $borrowing->fine_paid   = false;
            }

            if ($newStatus === 'Dipinjam') {
                $borrowing->return_date = null;

                if ($request->filled('borrow_date')) {
                    $borrowing->borrow_date = $request->borrow_date;
                }

                if ($request->filled('due_date')) {
                    $borrowing->due_date = $request->due_date;
                }

                if ($oldStatus !== 'Terlambat') {
                    $borrowing->late_days = 0;
                    $borrowing->fine_amount = 0;
                    $borrowing->fine_paid = false;
                }
            }

            if ($newStatus === 'Dipinjam' && (empty($borrowing->borrow_date) || empty($borrowing->due_date))) {
                $duration = (int) ($request->duration ?? $borrowing->duration ?? 1);
                $now = Carbon::now();
                $borrowing->borrow_date = $borrowing->borrow_date ?: $now->toDateString();
                $borrowing->due_date    = $borrowing->due_date ?: $now->copy()->addDays($duration)->toDateString();
            }

            if ($request->has('fine_paid')) {
                $borrowing->fine_paid = (bool) $request->fine_paid;
            }

            $borrowing->member_id     = $request->member_id ?? null;
            $borrowing->student_name  = $request->student_name;
            $borrowing->student_nis   = $request->student_nis;
            $borrowing->student_class = $request->student_class;
            $borrowing->book_id       = $newBookId;

            if ($request->filled('duration')) {
                $borrowing->duration = (int) $request->duration;
            }

            $borrowing->status = $newStatus;
            $borrowing->save();
        });

        $tab = $request->input('active_tab', 'pengajuan');

        return redirect()->route('borrowings.index', ['active_tab' => $tab])
            ->with('success', 'Data peminjaman berhasil diupdate.');
    }

    public function markFinePaid(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'Terlambat') {
            return redirect()->route('borrowings.index', [
                'active_tab' => 'terlambat',
                'fine_start' => $request->input('fine_start'),
                'fine_end'   => $request->input('fine_end'),
            ])->with('error', 'Hanya data terlambat yang bisa dikonfirmasi dendanya.');
        }

        $borrowing->fine_paid = true;
        $borrowing->save();

        $tab = $request->input('active_tab', $request->input('redirect_tab', 'terlambat'));

        return redirect()->route('borrowings.index', [
            'active_tab' => $tab,
            'fine_start' => $request->input('fine_start'),
            'fine_end'   => $request->input('fine_end'),
        ])->with('success', 'Pembayaran denda berhasil dikonfirmasi.');
    }

    public function markFineUnpaid(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'Terlambat') {
            return redirect()->route('borrowings.index', [
                'active_tab' => 'terlambat',
                'fine_start' => $request->input('fine_start'),
                'fine_end'   => $request->input('fine_end'),
            ])->with('error', 'Hanya data terlambat yang bisa diubah status dendanya.');
        }

        $borrowing->fine_paid = false;
        $borrowing->save();

        $tab = $request->input('active_tab', $request->input('redirect_tab', 'terlambat'));

        return redirect()->route('borrowings.index', [
            'active_tab' => $tab,
            'fine_start' => $request->input('fine_start'),
            'fine_end'   => $request->input('fine_end'),
        ])->with('success', 'Status pembayaran denda diubah menjadi belum bayar.');
    }

    public function destroy(Request $request, Borrowing $borrowing)
    {
        DB::transaction(function () use ($borrowing) {
            if (in_array($borrowing->status, ['Dipinjam', 'Terlambat'], true)) {
                $book = Book::where('id', $borrowing->book_id)->lockForUpdate()->first();
                if ($book) {
                    $book->increment('stock');
                }
            }

            $borrowing->delete();
        });

        $tab = $request->input('active_tab', 'pengajuan');

        return redirect()->route('borrowings.index', ['active_tab' => $tab])
            ->with('success', 'Data berhasil dihapus.');
    }
}
