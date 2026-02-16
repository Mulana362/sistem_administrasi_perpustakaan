<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // kalau sebelumnya kamu udah punya expired_at, biarin aja.
            // kita pakai expires_at yang dipakai controller
            if (!Schema::hasColumn('borrowings', 'expires_at')) {
                $table->dateTime('expires_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('borrowings', 'extend_count')) {
                $table->unsignedTinyInteger('extend_count')->default(0)->after('expires_at');
            }

            if (!Schema::hasColumn('borrowings', 'last_extended_at')) {
                $table->dateTime('last_extended_at')->nullable()->after('extend_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (Schema::hasColumn('borrowings', 'last_extended_at')) {
                $table->dropColumn('last_extended_at');
            }
            if (Schema::hasColumn('borrowings', 'extend_count')) {
                $table->dropColumn('extend_count');
            }
            if (Schema::hasColumn('borrowings', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};
