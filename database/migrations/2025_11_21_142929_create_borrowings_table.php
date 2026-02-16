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

            /*
            |------------------------------------------------------------------
            | RELASI MEMBER (opsional – dari peminjaman siswa)
            |------------------------------------------------------------------
            */
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('members')
                ->nullOnDelete();

            /*
            |------------------------------------------------------------------
            | DATA SISWA (snapshot, walau member berubah)
            |------------------------------------------------------------------
            */
            $table->string('student_name');
            $table->string('student_nis');
            $table->string('student_class');

            /*
            |------------------------------------------------------------------
            | RELASI BUKU
            |------------------------------------------------------------------
            */
            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();

            /*
            |------------------------------------------------------------------
            | DATA TANGGAL
            |------------------------------------------------------------------
            | Diajukan  → borrow_date & due_date boleh NULL
            | Dipinjam → wajib terisi
            */
            $table->date('borrow_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('return_date')->nullable();

            /*
            |------------------------------------------------------------------
            | DURASI
            |------------------------------------------------------------------
            */
            $table->unsignedTinyInteger('duration')->default(7);

            /*
            |------------------------------------------------------------------
            | STATUS PEMINJAMAN
            |------------------------------------------------------------------
            */
            $table->enum('status', [
                'Diajukan',
                'Dipinjam',
                'Kembali',
                'Terlambat'
            ])->default('Diajukan');

            /*
            |------------------------------------------------------------------
            | KADALUARSA & SOFT DELETE
            |------------------------------------------------------------------
            */
            $table->timestamp('expired_at')->nullable(); // kapan auto kadaluarsa
            $table->softDeletes();                       // deleted_at

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
