<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;

class PurgeOldRequests extends Command
{
    protected $signature = 'borrowings:purge-old-requests';
    protected $description = 'Hapus pengajuan (status Diajukan) yang lebih dari 5 hari';

    public function handle()
    {
        $deleted = Borrowing::where('status', 'Diajukan')
            ->where('created_at', '<', now()->subDays(5))
            ->delete();

        $this->info("Deleted {$deleted} old requests.");
        return Command::SUCCESS;
    }
}
