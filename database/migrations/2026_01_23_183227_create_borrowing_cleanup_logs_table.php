<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('borrowing_cleanup_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('borrowing_id')->nullable(); // bisa null kalau data udah hilang
            $table->string('student_nis')->nullable();
            $table->string('student_name')->nullable();
            $table->unsignedBigInteger('book_id')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('deleted_at')->nullable(); // kapan di-soft delete
            $table->string('reason')->default('AUTO_EXPIRED_5_DAYS');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_cleanup_logs');
    }
};
