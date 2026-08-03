<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use Illuminate\Console\Command;

class MarkOverdueBorrowings extends Command
{
    protected $signature = 'borrowings:mark-overdue';
    protected $description = 'Tandai peminjaman yang lewat jatuh tempo sebagai terlambat';

    public function handle(): void
    {
        $count = Borrowing::where('status', 'dipinjam')
            ->whereDate('due_date', '<', now())
            ->update(['status' => 'terlambat']);

        $this->info("{$count} peminjaman ditandai terlambat.");
    }
}