<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom member_id ke tabel borrowings
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {

            // Tambahkan hanya jika belum ada
            if (!Schema::hasColumn('borrowings', 'member_id')) {
                $table->foreignId('member_id')
                    ->after('id')
                    ->constrained('members')   // foreign key ke tabel members
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Hapus kolom member_id jika rollback
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {

            if (Schema::hasColumn('borrowings', 'member_id')) {
                $table->dropForeign(['member_id']);
                $table->dropColumn('member_id');
            }
        });
    }
};
