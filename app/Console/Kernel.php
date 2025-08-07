<?php

namespace App\Console;

use App\Jobs\ClearDuplicateEmails;
use App\Jobs\ClearTemporaryBroadcastFiles;
use App\Jobs\ProcessGroupMailbox;
use App\Jobs\ResetDemoSite;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(ProcessGroupMailbox::class)
	        ->everyFiveMinutes()
            ->sentryMonitor('process-group-mailbox')
	        ->thenPing(config('app.heartbeats.process_group_mailbox'));

        $schedule->job(ClearDuplicateEmails::class)
	        ->daily()
            ->sentryMonitor('clear-duplicate-emails')
            ->thenPing(config('app.heartbeats.clear_duplicate_emails'));

        $schedule->command('telescope:prune --hours=72')
            ->daily()
            ->at('16:00') // Midnight Perth
            ->sentryMonitor('telescope:prune')
            ->thenPing(config('app.heartbeats.telescope_prune'));

        $schedule->command('backup:clean')
            ->daily()
            ->at('18:00') // 2 am Perth
            ->sentryMonitor('backup:clean')
            ->thenPing(config('app.heartbeats.backup_clean'));

        $schedule->command('backup:run')
            ->daily()
            ->at('19:00') // 3 am Perth
            ->sentryMonitor('backup:run')
            ->thenPing(config('app.heartbeats.backup_run'));

        $schedule->job(ClearTemporaryBroadcastFiles::class)
            ->daily()
            ->at('21:00') // 5 am Perth
            ->sentryMonitor('clear-temporary-broadcast-files')
            ->thenPing(config('app.heartbeats.clear_temporary_broadcast_files'));

        $schedule->job(ResetDemoSite::class)
            ->weekly()
            ->at('23:00') // 7 am Perth
            ->sentryMonitor('reset-demo-site')
            ->thenPing(config('app.heartbeats.reset_demo_site'));
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
