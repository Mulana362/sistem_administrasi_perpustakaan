<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'book_code',
        'title',
        'description',
        'author',
        'publisher',
        'year',
        'stock',
        'cover',
        'no_rak',
    ];
}
