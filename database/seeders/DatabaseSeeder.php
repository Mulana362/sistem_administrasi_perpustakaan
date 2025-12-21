<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Import model yang digunakan
use App\Models\Member;
use App\Models\Book;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jalankan seeders Members & Books
        $this->call([
            MemberSeeder::class,
            BookSeeder::class,
        ]);
    }
}
