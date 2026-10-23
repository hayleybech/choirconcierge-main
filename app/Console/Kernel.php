<?php

namespace App\Console;

use App\Jobs\ClearTemporaryBroadcastFiles;
use App\Jobs\MarkAbsencesAfterEvents;
use App\Jobs\ProcessGroupMailbox;
use App\Jobs\ResetDemoSite;
use App\Jobs\SendAttendanceReports;
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
            ->everyMinute()
            ->sentryMonitor('process-group-mailbox')
            ->thenPingIf(filled(config('app.heartbeats.process_group_mailbox')), config('app.heartbeats.process_group_mailbox'));

        $schedule->command('telescope:prune --hours=72')
            ->daily()
            ->at('16:00') // Midnight Perth
            ->sentryMonitor('telescope:prune')
            ->thenPingIf(filled(config('app.heartbeats.telescope_prune')), config('app.heartbeats.telescope_prune'));

        $schedule->command('backup:clean')
            ->daily()
            ->at('18:00') // 2 am Perth
            ->sentryMonitor('backup:clean')
            ->thenPingIf(filled(config('app.heartbeats.backup_clean')), config('app.heartbeats.backup_clean'));

        $schedule->command('backup:run')
            ->daily()
            ->at('19:00') // 3 am Perth
            ->sentryMonitor('backup:run')
            ->thenPingIf(filled(config('app.heartbeats.backup_run')), config('app.heartbeats.backup_run'));

        $schedule->job(ClearTemporaryBroadcastFiles::class)
            ->daily()
            ->at('21:00') // 5 am Perth
            ->sentryMonitor('clear-temporary-broadcast-files')
            ->thenPingIf(filled(config('app.heartbeats.clear_temporary_broadcast_files')), config('app.heartbeats.clear_temporary_broadcast_files'));

        $schedule->job(ResetDemoSite::class)
            ->weekly()
            ->at('23:00') // 7 am Perth
            ->sentryMonitor('reset-demo-site')
            ->thenPingIf(filled(config('app.heartbeats.reset_demo_site')), config('app.heartbeats.reset_demo_site'));

        $schedule->job(MarkAbsencesAfterEvents::class)
            ->hourly();

        $schedule->job(SendAttendanceReports::class)
            ->hourly();
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
