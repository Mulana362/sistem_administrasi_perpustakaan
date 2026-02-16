<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE borrowings MODIFY borrow_date DATE NULL");
        DB::statement("ALTER TABLE borrowings MODIFY due_date DATE NULL");
    }

    public function down(): void
    {
        // kalau rollback, balik NOT NULL (hati-hati kalau ada NULL)
        DB::statement("ALTER TABLE borrowings MODIFY borrow_date DATE NOT NULL");
        DB::statement("ALTER TABLE borrowings MODIFY due_date DATE NOT NULL");
    }
};
