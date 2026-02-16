<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('created_at');
            $table->unsignedTinyInteger('extend_count')->default(0)->after('expires_at');
            $table->timestamp('last_extended_at')->nullable()->after('extend_count');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'extend_count', 'last_extended_at']);
        });
    }
};
