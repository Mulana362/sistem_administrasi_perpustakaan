<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE borrowings
            MODIFY status ENUM(
                'Diajukan',
                'Dipinjam',
                'Kembali',
                'Terlambat'
            ) NOT NULL DEFAULT 'Diajukan'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE borrowings
            MODIFY status ENUM(
                'Dipinjam',
                'Kembali',
                'Terlambat'
            ) NOT NULL DEFAULT 'Dipinjam'
        ");
    }
};
