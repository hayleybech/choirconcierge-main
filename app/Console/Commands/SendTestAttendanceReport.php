<?php

namespace App\Console\Commands;

use App\Mail\AttendanceReport;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestAttendanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-test-report {event} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test version of the attendance report for a given event and a given email address';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $eventId = $this->argument('event');
        $email = $this->argument('email');

        $event = Event::find($eventId);

        if (! $event) {
            $this->error("Event with ID {$eventId} not found.");
            return;
        }

        tenancy()->initialize($event->tenant_id);

        Mail::to($email)->send(new AttendanceReport($event));

        tenancy()->end();

        $this->info("Attendance report for event '{$event->title}' sent to {$email}.");
    }
}
