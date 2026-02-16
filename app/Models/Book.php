<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Nama tabel (opsional kalau sudah 'books')
    protected $table = 'books';

    // Kolom yang boleh diisi mass-assignment
    protected $fillable = [
    'book_code',
    'title',
    'description',
    'author',
    'publisher',
    'year',
    'stock',
    'cover',
];

}
