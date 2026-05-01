<?php

namespace App\Http\Controllers;

use App\Models\BookIssue;
use App\Models\Borrowing;
use Illuminate\Http\Request;

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

        $this->decreaseLostBookStockIfNeeded($bookIssue);

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

        $oldStatus = $bookIssue->status;

        $bookIssue->update([
            'fine_amount' => $request->fine_amount ?? 0,
            'replacement_required' => $request->replacement_required ?? 0,
            'replacement_note' => $request->replacement_note,
            'status' => $request->status,
            'note' => $request->note,
        ]);

        $this->decreaseLostBookStockIfNeeded($bookIssue, $oldStatus);

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
        $oldStatus = $bookIssue->status;

        $bookIssue->update([
            'status' => 'Selesai',
        ]);

        $this->decreaseLostBookStockIfNeeded($bookIssue, $oldStatus);

        return redirect()
            ->route('borrowings.index', ['active_tab' => 'issue'])
            ->with('success', 'Status kasus diubah menjadi Selesai.');
    }

    private function decreaseLostBookStockIfNeeded(BookIssue $bookIssue, ?string $oldStatus = null): void
    {
        $bookIssue->loadMissing('book');

        if ($bookIssue->issue_type !== 'Hilang') {
            return;
        }

        if ($bookIssue->status !== 'Selesai') {
            return;
        }

        if ($oldStatus === 'Selesai') {
            return;
        }

        $book = $bookIssue->book;

        if (!$book) {
            return;
        }

        $stockColumn = null;

        foreach (['stock', 'stok', 'jumlah_stok'] as $column) {
            if (array_key_exists($column, $book->getAttributes())) {
                $stockColumn = $column;
                break;
            }
        }

        if (!$stockColumn) {
            return;
        }

        if ((int) $book->{$stockColumn} <= 0) {
            return;
        }

        $book->decrement($stockColumn);
    }
}
