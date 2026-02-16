<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan semua status lama masih aman
        DB::statement("
            ALTER TABLE borrowings
            MODIFY status
            ENUM('Diajukan','Dipinjam','Kembali','Terlambat')
            NOT NULL DEFAULT 'Diajukan'
        ");
    }

    public function down(): void
    {
        // Kalau rollback: balik ke enum lama (tanpa Diajukan)
        // NOTE: Kalau ada data 'Diajukan', rollback akan error.
        DB::statement("
            ALTER TABLE borrowings
            MODIFY status
            ENUM('Dipinjam','Kembali','Terlambat')
            NOT NULL DEFAULT 'Dipinjam'
        ");
    }
};
