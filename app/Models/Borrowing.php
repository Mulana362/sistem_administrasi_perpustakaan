<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrowing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'book_id',
        'student_name',
        'student_nis',
        'student_class',
        'borrow_date',
        'due_date',
        'return_date',
        'duration',
        'status',
        'expired_at',

        // ✅ tambahan untuk fitur perpanjang
        'extend_count',
        'last_extended_at',
    ];

    protected $casts = [
        'borrow_date'       => 'date',
        'due_date'          => 'date',
        'return_date'       => 'date',
        'expired_at'        => 'datetime',

        // ✅ cast tambahan
        'last_extended_at'  => 'datetime',
        'extend_count'      => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function scopeDiajukan($query)
    {
        return $query->where('status', 'Diajukan');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'Diajukan')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now());
    }
}
