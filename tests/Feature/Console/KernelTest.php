<?php

use App\Console\Kernel;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Console\Scheduling\Schedule;

it('skips the mailbox heartbeat when its URL is empty', function (?string $heartbeatUrl) {
    config(['app.heartbeats.process_group_mailbox' => $heartbeatUrl]);

    $schedule = new Schedule;
    $kernel = new class(app(), app(Dispatcher::class)) extends Kernel {
        public function registerSchedule(Schedule $schedule): void
        {
            $this->schedule($schedule);
        }
    };
    $kernel->registerSchedule($schedule);
    $mailboxEvent = $schedule->events()[0];

    expect(fn () => $mailboxEvent->finish(app(), 0))
        ->not->toThrow(InvalidArgumentException::class);
})->with([
    'missing' => null,
    'empty' => '',
    'whitespace-only' => '   ',
]);
