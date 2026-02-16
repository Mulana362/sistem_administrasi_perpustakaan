<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;

class CleanupBorrowingRequests extends Command
{
    protected $signature = 'borrowings:cleanup';
    protected $description = 'Hapus (soft delete) pengajuan peminjaman yang expired berdasarkan expired_at';

    public function handle(): int
    {
        $deleted = Borrowing::where('status', 'Diajukan')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->whereNull('deleted_at')
            ->delete();

        if ($deleted > 0) {
            $this->info("✅ {$deleted} pengajuan kadaluarsa dihapus (soft delete).");
        } else {
            $this->info("ℹ️ Tidak ada pengajuan kadaluarsa.");
        }

        // biar warning VSCode gak muncul
        return 0;
    }
}
