<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['title'=>'Matematika Dasar','author'=>'Slamet','publisher'=>'Gramedia','year'=>'2021'],
            ['title'=>'Bahasa Indonesia','author'=>'Rina','publisher'=>'Erlangga','year'=>'2020'],
            ['title'=>'Ilmu Pengetahuan Alam','author'=>'Budi','publisher'=>'Mediakom','year'=>'2022'],
            ['title'=>'Sejarah Indonesia','author'=>'Agus','publisher'=>'Pustaka','year'=>'2019'],
            ['title'=>'Bahasa Inggris','author'=>'Siti','publisher'=>'Oxford','year'=>'2023'],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
