<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
        });

        DB::statement('ALTER TABLE borrowings MODIFY book_id BIGINT UNSIGNED NULL');

        Schema::table('borrowings', function (Blueprint $table) {
            $table->foreign('book_id')
                ->references('id')
                ->on('books')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
        });

        DB::statement('ALTER TABLE borrowings MODIFY book_id BIGINT UNSIGNED NOT NULL');

        Schema::table('borrowings', function (Blueprint $table) {
            $table->foreign('book_id')
                ->references('id')
                ->on('books')
                ->cascadeOnDelete();
        });
    }
};
