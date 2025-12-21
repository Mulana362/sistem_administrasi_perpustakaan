<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();

            $table->string('name');              // Nama pengunjung
            $table->string('nis')->nullable();   // NIS (opsional)
            $table->string('class');             // Kelas
            $table->string('purpose')->nullable(); // Keperluan berkunjung
            $table->string('note')->nullable();    // Catatan tambahan
            $table->date('visit_date');            // Tanggal hadir

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('visitors');
    }
};
