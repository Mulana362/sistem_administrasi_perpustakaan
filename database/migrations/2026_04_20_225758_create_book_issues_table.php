<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained('borrowings')->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();

            $table->string('student_name');
            $table->string('student_nis', 50);
            $table->string('student_class', 50);

            $table->enum('issue_type', ['Hilang', 'Rusak']);
            $table->date('reported_at');

            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->boolean('replacement_required')->default(false);
            $table->string('replacement_note')->nullable();

            $table->enum('status', ['Dilaporkan', 'Diproses', 'Selesai'])->default('Dilaporkan');
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
