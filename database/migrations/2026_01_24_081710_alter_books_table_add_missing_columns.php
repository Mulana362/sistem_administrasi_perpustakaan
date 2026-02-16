<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBooksTableAddMissingColumns extends Migration
{
    public function up(): void
    {
        /**
         * Tambah kolom hanya kalau belum ada
         * (supaya tidak error Duplicate column)
         */
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'book_code')) {
                $table->string('book_code')->nullable()->unique()->after('id');
            }

            if (!Schema::hasColumn('books', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (!Schema::hasColumn('books', 'cover')) {
                $table->string('cover')->nullable()->after('description');
            }

            if (!Schema::hasColumn('books', 'stock')) {
                $table->integer('stock')->default(0)->after('year');
            }
        });

        /**
         * Ubah kolom year jadi nullable kalau kolom year ada
         * (change() dipisah supaya aman)
         */
        if (Schema::hasColumn('books', 'year')) {
            Schema::table('books', function (Blueprint $table) {
                $table->integer('year')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        /**
         * Drop kolom hanya kalau ada
         */
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'book_code')) {
                $table->dropColumn('book_code');
            }

            if (Schema::hasColumn('books', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('books', 'cover')) {
                $table->dropColumn('cover');
            }

            if (Schema::hasColumn('books', 'stock')) {
                $table->dropColumn('stock');
            }
        });

        /**
         * Balikin year jadi NOT NULL (kalau kolom year ada)
         */
        if (Schema::hasColumn('books', 'year')) {
            Schema::table('books', function (Blueprint $table) {
                $table->integer('year')->nullable(false)->change();
            });
        }
    }
}
