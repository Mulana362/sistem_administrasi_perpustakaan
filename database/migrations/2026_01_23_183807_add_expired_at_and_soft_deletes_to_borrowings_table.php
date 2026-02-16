<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (!Schema::hasColumn('borrowings', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('borrowings', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (Schema::hasColumn('borrowings', 'expired_at')) {
                $table->dropColumn('expired_at');
            }

            if (Schema::hasColumn('borrowings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
