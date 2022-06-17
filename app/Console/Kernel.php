<?php

namespace App\Console;

use App\Jobs\ClearDuplicateEmails;
use App\Jobs\ClearTemporaryBroadcastFiles;
use App\Jobs\ProcessGroupMailbox;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(ProcessGroupMailbox::class)->everyFiveMinutes();

        $schedule->job(ClearDuplicateEmails::class)->daily();

        $schedule->command('telescope:prune --hours=72')
            ->daily()
            ->at('16:00'); // Midnight Perth

        $schedule->command('backup:clean')
            ->daily()
            ->at('18:00'); // 2 am Perth

        $schedule->command('backup:run')
            ->daily()
            ->at('19:00'); // 3 am Perth

        $schedule->job(ClearTemporaryBroadcastFiles::class)
            ->daily()
            ->at('21:00'); // 5 am Perth
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
