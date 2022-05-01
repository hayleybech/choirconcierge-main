<?php

namespace App\Console;

use App\Jobs\ClearDuplicateEmails;
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
        // $schedule->command('inspire')
        //          ->hourly();

        if (config('queue.cron_fix')) {
            /*
             * The Dirty Cron Queue Fix
             *
             * Allow cron job for Laravel queues on shared hosting
             *
             * Only god knows how a QUEUED job could successfully keep the QUEUE listener running.
             *
             * @see https://papertank.com/blog/903/setup-laravel-queue-on-shared-hosting/
             * @see https://gist.github.com/davidrushton/b7229df4c73372402fc1#file-kernel-php-L29
             */
            $path = base_path();
            $schedule
                ->call(function () use ($path) {
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        // just repeatedly exec on Windows
                        $command = PHP_BINARY.' '.$path.'/artisan queue:work --tries=3 > /dev/null & echo $!';
                        $number = exec($command);
                    } else {
                        // check process on Linux
                        if (file_exists($path.'/queue.pid')) {
                            $pid = file_get_contents($path.'/queue.pid');
                            $result = exec("ps -p $pid --no-heading | awk '{print $1}'");
                            echo 'Checking queue worker';
                            $run = $result === '';
                        } else {
                            $run = true;
                        }
                        if ($run) {
                            //$command = '/usr/bin/php -c ' . $path .'/php.ini ' . $path . '/artisan queue:work --tries=3 > /dev/null & echo $!';
                            $command =
                                PHP_BINARY.
                                ' '.
                                $path.
                                '/artisan queue:work --tries=3 --stop-when-empty > /dev/null & echo $!';
                            $number = exec($command);
                            echo 'Running queue worker';
                            file_put_contents($path.'/queue.pid', $number);
                        }
                    }
                })
                ->name('monitor_queue_listener')
                ->everyMinute();
        }

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

    private function run_dirty_cron_queue_fix()
    {
    }
}
