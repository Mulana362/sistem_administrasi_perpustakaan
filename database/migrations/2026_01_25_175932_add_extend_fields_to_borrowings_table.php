<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtendFieldsToBorrowingsTable extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {

            // jumlah perpanjangan
            if (!Schema::hasColumn('borrowings', 'extend_count')) {
                $table->unsignedTinyInteger('extend_count')
                    ->default(0)
                    ->after('expired_at');
            }

            // waktu terakhir diperpanjang
            if (!Schema::hasColumn('borrowings', 'last_extended_at')) {
                $table->timestamp('last_extended_at')
                    ->nullable()
                    ->after('extend_count');
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

        });
    }
}
