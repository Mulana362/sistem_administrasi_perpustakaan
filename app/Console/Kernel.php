<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 🔥 AUTO HAPUS pengajuan (status: Diajukan) yang lebih dari 2 hari
        // Jalan 1x sehari (produksi)
        $schedule->command('borrowings:cleanup')->daily();

        // ⏱️ Kalau mau TEST cepat di lokal:
        // $schedule->command('borrowings:cleanup')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
