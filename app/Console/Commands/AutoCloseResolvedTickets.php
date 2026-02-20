<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupportTicket;

class AutoCloseResolvedTickets extends Command
{
    protected $signature = 'tickets:auto-close-resolved {--days= : Override auto close days}';
    protected $description = 'Auto-close resolved tickets older than N days';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('tickets.auto_close_days', 7));
        if ($days <= 0) {
            $this->info('Auto-close disabled (days <= 0).');
            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days);

        $count = SupportTicket::query()
            ->where('status', SupportTicket::STATUS_RESOLVED)
            ->whereNotNull('resolved_at')
            ->where('resolved_at', '<=', $cutoff)
            ->whereNull('closed_at')
            ->update([
                'status' => SupportTicket::STATUS_CLOSED,
                'closed_at' => now(),
                'close_reason' => 'auto_closed',
                'last_activity_at' => now(),
            ]);

        $this->info("Auto-closed {$count} tickets.");
        return self::SUCCESS;
    }
}
