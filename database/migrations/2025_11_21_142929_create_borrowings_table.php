<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();

            /**
             * DATA SISWA (Diisi manual, tanpa login)
             * A = Nama, B = NIS, Kelas
             */
            $table->string('student_name');      // contoh: "Rizky Ramadhan"
            $table->string('student_nis');       // contoh: "12345"
            $table->string('student_class');     // contoh: "8A"

            /**
             * RELASI KE BUKU
             */
            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();

            /**
             * DATA PEMINJAMAN
             */
            $table->date('borrow_date');         // tanggal pinjam (dipilih siswa)
            $table->date('due_date');            // otomatis = tanggal pinjam + 7 hari
            $table->date('return_date')->nullable(); // isi saat pengembalian

            /**
             * LAMA PINJAM (fixed 7 hari)
             */
            $table->unsignedTinyInteger('duration')->default(7);

            /**
             * STATUS PEMINJAMAN
             */
            $table->enum('status', ['Dipinjam', 'Kembali', 'Terlambat'])
                ->default('Dipinjam');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
