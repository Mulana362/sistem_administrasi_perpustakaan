<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookIssueController extends Controller
{
    public function index()
    {
        $bookIssues = BookIssue::with(['book', 'borrowing'])
            ->latest()
            ->get();

        return view('book-issues.index', compact('bookIssues'));
    }

    public function create(Request $request)
    {
        $borrowing = Borrowing::with('book')->findOrFail($request->borrowing_id);

        return view('book-issues.create', compact('borrowing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'borrowing_id' => 'required|exists:borrowings,id',
            'book_id' => 'nullable',
            'student_name' => 'required|string|max:255',
            'student_nis' => 'required|string|max:50',
            'student_class' => 'required|string|max:50',
            'issue_type' => 'required|in:Hilang,Rusak',
            'reported_at' => 'required|date',
            'fine_amount' => 'nullable|numeric|min:0',
            'replacement_required' => 'nullable|boolean',
            'replacement_note' => 'nullable|string|max:255',
            'status' => 'required|in:Dilaporkan,Diproses,Selesai',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $bookIssue = BookIssue::create([
                'borrowing_id' => $request->borrowing_id,
                'book_id' => $request->book_id,
                'student_name' => $request->student_name,
                'student_nis' => $request->student_nis,
                'student_class' => $request->student_class,
                'issue_type' => $request->issue_type,
                'reported_at' => $request->reported_at,
                'fine_amount' => $request->fine_amount ?? 0,
                'replacement_required' => $request->replacement_required ?? 0,
                'replacement_note' => $request->replacement_note,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            $this->finishBorrowingIssueIfNeeded($bookIssue);
        });

        return redirect()
            ->route('borrowings.index', ['active_tab' => 'issue'])
            ->with('success', 'Kasus berhasil ditambahkan.');
    }

    public function edit(BookIssue $bookIssue)
    {
        $bookIssue->load('book', 'borrowing');

        return view('book-issues.edit', compact('bookIssue'));
    }

    public function update(Request $request, BookIssue $bookIssue)
    {
        $request->validate([
            'fine_amount' => 'nullable|numeric|min:0',
            'replacement_required' => 'nullable|boolean',
            'replacement_note' => 'nullable|string|max:255',
            'status' => 'required|in:Dilaporkan,Diproses,Selesai',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $bookIssue) {
            $oldStatus = $bookIssue->status;

            $bookIssue->update([
                'fine_amount' => $request->fine_amount ?? 0,
                'replacement_required' => $request->replacement_required ?? 0,
                'replacement_note' => $request->replacement_note,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            $this->finishBorrowingIssueIfNeeded($bookIssue, $oldStatus);
        });

        return redirect()
            ->route('borrowings.index', ['active_tab' => 'issue'])
            ->with('success', 'Kasus berhasil diperbarui.');
    }

    public function destroy(BookIssue $bookIssue)
    {
        $bookIssue->delete();

        return redirect()
            ->route('borrowings.index', ['active_tab' => 'issue'])
            ->with('success', 'Kasus berhasil dihapus.');
    }

    public function markProcessing(BookIssue $bookIssue)
    {
        $bookIssue->update([
            'status' => 'Diproses',
        ]);

        return redirect()
            ->route('borrowings.index', ['active_tab' => 'issue'])
            ->with('success', 'Status kasus diubah menjadi Diproses.');
    }

    public function markFinished(BookIssue $bookIssue)
    {
        DB::transaction(function () use ($bookIssue) {
            $oldStatus = $bookIssue->status;

            $bookIssue->update([
                'status' => 'Selesai',
            ]);

            $this->finishBorrowingIssueIfNeeded($bookIssue, $oldStatus);
        });

        return redirect()
            ->route('borrowings.index', ['active_tab' => 'issue'])
            ->with('success', 'Status kasus diubah menjadi Selesai.');
    }

    private function finishBorrowingIssueIfNeeded(BookIssue $bookIssue, ?string $oldStatus = null): void
    {
        $bookIssue->refresh();

        if ($bookIssue->status !== 'Selesai') {
            return;
        }

        if ($oldStatus === 'Selesai') {
            return;
        }

        $borrowing = Borrowing::where('id', $bookIssue->borrowing_id)
            ->lockForUpdate()
            ->first();

        if (!$borrowing) {
            return;
        }

        $wasStillBorrowed = in_array($borrowing->status, ['Dipinjam', 'Terlambat'], true)
            && is_null($borrowing->return_date);

        if (!$wasStillBorrowed) {
            return;
        }

        $borrowing->status = $bookIssue->issue_type;
        $borrowing->return_date = now()->toDateString();
        $borrowing->save();

        if ($bookIssue->issue_type === 'Rusak' || $this->shouldIncreaseStockForLostReplacement($bookIssue)) {
            $this->increaseBookStock($bookIssue);
        }
    }

    private function shouldIncreaseStockForLostReplacement(BookIssue $bookIssue): bool
    {
        return $bookIssue->issue_type === 'Hilang'
            && (int) ($bookIssue->replacement_required ?? 0) === 1;
    }

    private function increaseBookStock(BookIssue $bookIssue): void
    {
        $bookId = $bookIssue->book_id;

        if (!$bookId && $bookIssue->borrowing) {
            $bookId = $bookIssue->borrowing->book_id;
        }

        if (!$bookId) {
            return;
        }

        $book = Book::where('id', $bookId)
            ->lockForUpdate()
            ->first();

        if (!$book) {
            return;
        }

        $stockColumn = $this->getStockColumn($book);

        if (!$stockColumn) {
            return;
        }

        $book->increment($stockColumn);
    }

    private function getStockColumn(Book $book): ?string
    {
        foreach (['stock', 'stok', 'jumlah_stok'] as $column) {
            if (array_key_exists($column, $book->getAttributes())) {
                return $column;
            }
        }

        return null;
    }
}
