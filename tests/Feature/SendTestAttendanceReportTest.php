<?php
namespace Tests\Feature;

use App\Mail\AttendanceReport;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;
use function Pest\Laravel\artisan;

it('sends test attendance report email', function () {
    Mail::fake();

    // 1. Setup tenant and event
    $event = Event::factory()->create([
        'title' => 'Test Event Report',
    ]);

    $testEmail = 'tester@example.com';

    // 2. Run the command
    // We end tenancy before because the command expects to find the event and then initialize its own tenancy
    tenancy()->end();

    artisan('attendance:send-test-report', [
        'event' => $event->id,
        'email' => $testEmail,
    ])
        ->expectsOutput("Attendance report for event 'Test Event Report' sent to {$testEmail}.")
        ->assertSuccessful();

    // 3. Assertions
    Mail::assertSent(AttendanceReport::class, function ($mail) use ($testEmail, $event) {
        return $mail->hasTo($testEmail) &&
               $mail->event->id === $event->id;
    });
});
