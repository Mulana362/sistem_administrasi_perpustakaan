<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (!Schema::hasColumn('borrowings', 'late_days')) {
                $table->unsignedInteger('late_days')->default(0)->after('return_date');
            }

            if (!Schema::hasColumn('borrowings', 'fine_amount')) {
                $table->unsignedBigInteger('fine_amount')->default(0)->after('late_days');
            }

            if (!Schema::hasColumn('borrowings', 'fine_paid')) {
                $table->boolean('fine_paid')->default(false)->after('fine_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (Schema::hasColumn('borrowings', 'fine_paid')) {
                $table->dropColumn('fine_paid');
            }

            if (Schema::hasColumn('borrowings', 'fine_amount')) {
                $table->dropColumn('fine_amount');
            }

            if (Schema::hasColumn('borrowings', 'late_days')) {
                $table->dropColumn('late_days');
            }
        });
    }
};
