<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

// command bawaan (JANGAN DIHAPUS)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| 🔥 SCHEDULER OTOMATIS
|--------------------------------------------------------------------------
| Auto hapus pengajuan peminjaman yang:
| - status = Diajukan
| - umur > 5 hari
| - TANPA klik / buka halaman / ketik manual
|--------------------------------------------------------------------------
*/

// jalan otomatis 1x sehari (aman & realistis)
Schedule::command('borrowings:cleanup')->daily();

// ⛔ jangan aktifkan ini kecuali buat TEST cepat
// Schedule::command('borrowings:cleanup')->everyMinute();
