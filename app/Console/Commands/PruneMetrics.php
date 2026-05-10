<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('metrics:prune {days=7}')]
#[Description('Delete old metrics to save space')]
class PruneMetrics extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');
        $count = \App\Models\Metric::where('created_at', '<', now()->subDays($days))->delete();
        $this->info("Eliminadas $count métricas antiguas (más de $days días).");
    }
}
