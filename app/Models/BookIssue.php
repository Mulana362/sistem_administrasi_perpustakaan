<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    protected $fillable = [
        'borrowing_id',
        'book_id',
        'student_name',
        'student_nis',
        'student_class',
        'issue_type',
        'reported_at',
        'fine_amount',
        'replacement_required',
        'replacement_note',
        'status',
        'note',
    ];

    protected $casts = [
        'reported_at' => 'date',
        'replacement_required' => 'boolean',
        'fine_amount' => 'decimal:2',
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
