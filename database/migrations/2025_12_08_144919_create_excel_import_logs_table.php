<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_import_logs', function (Blueprint $table) {
            $table->id();

            // 'books' atau 'members'
            $table->string('type');

            // nama file dan path simpan
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();

            // jumlah data baru yang dibuat
            $table->unsignedInteger('created_count')->default(0);

            // simpan ID record yang DIBUAT oleh import (untuk bisa dihapus lagi)
            $table->longText('created_ids')->nullable(); // JSON array of IDs

            // waktu import (supaya bisa di-order)
            $table->timestamp('imported_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_import_logs');
    }
};
