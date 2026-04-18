<?php

namespace Tests\Feature\Mail;

use App\Mail\AttendanceReport;
use App\Models\Attendance;
use App\Models\Enrolment;
use App\Models\Event;
use App\Models\Membership;
use App\Models\VoicePart;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AttendanceReportTest extends TestCase
{
    /** @test */
    public function it_groups_absent_singers_by_voice_part()
    {
        $event = Event::factory()->create([
            'title' => 'Rehearsal',
            'start_date' => now()->toDateTimeString(),
        ]);

        $soprano = VoicePart::factory()->create(['title' => 'Soprano']);
        $alto = VoicePart::factory()->create(['title' => 'Alto']);

        $ensemble = $event->ensembles()->first() ?: \App\Models\Ensemble::factory()->create();
        if (! $event->ensembles->contains($ensemble)) {
            $event->ensembles()->attach($ensemble);
        }

        $singer1 = Membership::factory()->create();
        $singer1->user->update(['first_name' => 'Alice', 'last_name' => 'Soprano']);
        Enrolment::factory()->create([
            'membership_id' => $singer1->id,
            'voice_part_id' => $soprano->id,
            'ensemble_id' => $ensemble->id,
        ]);
        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer1->id,
            'response' => 'absent',
        ]);

        $singer2 = Membership::factory()->create();
        $singer2->user->update(['first_name' => 'Anna', 'last_name' => 'Alto']);
        Enrolment::factory()->create([
            'membership_id' => $singer2->id,
            'voice_part_id' => $alto->id,
            'ensemble_id' => $ensemble->id,
        ]);
        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer2->id,
            'response' => 'absent_apology',
        ]);

        $singer3 = Membership::factory()->create();
        $singer3->user->update(['first_name' => 'Abby', 'last_name' => 'Alto 2']);
        Enrolment::factory()->create([
            'membership_id' => $singer3->id,
            'voice_part_id' => $alto->id,
            'ensemble_id' => $ensemble->id,
        ]);
        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer3->id,
            'response' => 'late_deemed_absent',
        ]);

        $mailable = new AttendanceReport($event);

        $html = $mailable->render();

        $this->assertStringContainsString('Soprano', $html);
        $this->assertStringContainsString('Alice Soprano', $html);
        $this->assertStringContainsString('Alto', $html);
        $this->assertStringContainsString('Anna Alto', $html);
        $this->assertStringContainsString('Abby Alto 2', $html);

        // Verify order: Alto before Soprano (alphabetical)
        $sopranoPos = strpos($html, 'Soprano');
        $altoPos = strpos($html, 'Alto');
        $this->assertLessThan($sopranoPos, $altoPos, 'Alto should appear before Soprano alphabetically');
    }
}
