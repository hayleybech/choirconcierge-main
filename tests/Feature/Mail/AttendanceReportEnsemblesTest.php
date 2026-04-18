<?php

namespace Tests\Feature\Mail;

use App\Mail\AttendanceReport;
use App\Models\Attendance;
use App\Models\Enrolment;
use App\Models\Ensemble;
use App\Models\Event;
use App\Models\Membership;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceReportEnsemblesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_groups_by_voice_part_when_single_ensemble_exists(): void
    {
        $event = Event::factory()->create(['title' => 'Single Ensemble Event']);
        $ensemble = Ensemble::factory()->create(['name' => 'Choir A']);
        $event->ensembles()->attach($ensemble);

        $soprano = VoicePart::factory()->create(['title' => 'Soprano']);
        
        $singer = Membership::factory()->create();
        $singer->user->update(['first_name' => 'Alice', 'last_name' => 'Singer']);
        
        Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'voice_part_id' => $soprano->id,
            'ensemble_id' => $ensemble->id,
        ]);
        
        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
        ]);

        $mailable = new AttendanceReport($event);
        $html = $mailable->render();

        $this->assertStringContainsString('Soprano', $html);
        $this->assertStringContainsString('Alice Singer', $html);
        $this->assertStringNotContainsString('Choir A', $html); // Should not show ensemble name if only one
    }

    public function test_it_groups_by_ensemble_and_voice_part_when_multiple_ensembles_exist(): void
    {
        $event = Event::factory()->create(['title' => 'Multi Ensemble Event']);
        $ensemble1 = Ensemble::factory()->create(['name' => 'Choir A']);
        $ensemble2 = Ensemble::factory()->create(['name' => 'Choir B']);
        $event->ensembles()->attach([$ensemble1->id, $ensemble2->id]);

        $soprano = VoicePart::factory()->create(['title' => 'Soprano']);
        $alto = VoicePart::factory()->create(['title' => 'Alto']);
        
        // Singer in Choir A
        $singer1 = Membership::factory()->create();
        $singer1->user->update(['first_name' => 'Alice', 'last_name' => 'ChoirA']);
        Enrolment::factory()->create([
            'membership_id' => $singer1->id,
            'voice_part_id' => $soprano->id,
            'ensemble_id' => $ensemble1->id,
        ]);
        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer1->id,
            'response' => 'absent',
        ]);

        // Singer in Choir B
        $singer2 = Membership::factory()->create();
        $singer2->user->update(['first_name' => 'Bob', 'last_name' => 'ChoirB']);
        Enrolment::factory()->create([
            'membership_id' => $singer2->id,
            'voice_part_id' => $alto->id,
            'ensemble_id' => $ensemble2->id,
        ]);
        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer2->id,
            'response' => 'absent',
        ]);

        $mailable = new AttendanceReport($event);
        $html = $mailable->render();

        $this->assertStringContainsString('Choir A', $html);
        $this->assertStringContainsString('Soprano', $html);
        $this->assertStringContainsString('Alice ChoirA', $html);
        
        $this->assertStringContainsString('Choir B', $html);
        $this->assertStringContainsString('Alto', $html);
        $this->assertStringContainsString('Bob ChoirB', $html);
    }

    public function test_it_does_not_show_no_part_when_voice_part_is_assigned(): void
    {
        $event = Event::factory()->create(['title' => 'Single Ensemble Event']);
        $ensemble = Ensemble::factory()->create(['name' => 'Choir A']);
        $event->ensembles()->attach($ensemble);

        $soprano = VoicePart::factory()->create(['title' => 'Soprano']);

        $singer = Membership::factory()->create();
        $singer->user->update(['first_name' => 'Alice', 'last_name' => 'Singer']);

        Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'voice_part_id' => $soprano->id,
            'ensemble_id' => $ensemble->id,
        ]);

        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
        ]);

        $mailable = new AttendanceReport($event);
        $html = $mailable->render();

        $this->assertStringContainsString('Soprano', $html);
        $this->assertStringContainsString('Alice Singer', $html);
        $this->assertStringNotContainsString('No Part', $html);
    }

    public function test_it_does_not_show_no_part_when_multiple_enrolments_exist_and_one_is_relevant(): void
    {
        $event = Event::factory()->create(['title' => 'Event with Choir A']);
        $ensembleA = Ensemble::factory()->create(['name' => 'Choir A']);
        $ensembleB = Ensemble::factory()->create(['name' => 'Choir B']);
        $event->ensembles()->attach($ensembleA);

        $soprano = VoicePart::factory()->create(['title' => 'Soprano']);

        $singer = Membership::factory()->create();
        $singer->user->update(['first_name' => 'Alice', 'last_name' => 'Singer']);

        // Relevant enrolment
        Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'voice_part_id' => $soprano->id,
            'ensemble_id' => $ensembleA->id,
        ]);

        // Irrelevant enrolment with NO voice part
        Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'voice_part_id' => null,
            'ensemble_id' => $ensembleB->id,
        ]);

        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
        ]);

        $mailable = new AttendanceReport($event);
        $html = $mailable->render();

        $this->assertStringContainsString('Soprano', $html);
        $this->assertStringContainsString('Alice Singer', $html);
        $this->assertStringNotContainsString('No Part', $html);
    }

    public function test_it_does_not_show_no_part_when_event_has_no_ensembles_but_singer_has_enrolments(): void
    {
        $event = Event::factory()->create(['title' => 'Event with No Ensembles']);
        // event->ensembles is empty
        
        $soprano = VoicePart::factory()->create(['title' => 'Soprano']);
        $ensemble = Ensemble::factory()->create(['name' => 'Choir A']);

        $singer = Membership::factory()->create();
        $singer->user->update(['first_name' => 'Alice', 'last_name' => 'Singer']);

        Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'voice_part_id' => $soprano->id,
            'ensemble_id' => $ensemble->id,
        ]);

        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
        ]);

        $mailable = new AttendanceReport($event);
        $html = $mailable->render();

        // When event has no ensembles, it should probably still try to find a voice part if possible, 
        // OR it currently falls into "No Ensemble" / "No Part" logic.
        
        // Let's see what happens.
        $this->assertStringContainsString('Alice Singer', $html);
        $this->assertStringContainsString('Soprano', $html);
        $this->assertStringNotContainsString('No Part', $html);
    }

    public function test_it_does_not_show_no_part_when_multiple_enrolments_exist_and_none_are_relevant(): void
    {
        $event = Event::factory()->create(['title' => 'Event with Choir C']);
        $ensembleA = Ensemble::factory()->create(['name' => 'Choir A']);
        $ensembleB = Ensemble::factory()->create(['name' => 'Choir B']);
        $ensembleC = Ensemble::factory()->create(['name' => 'Choir C']);
        $event->ensembles()->attach($ensembleC);

        $soprano = VoicePart::factory()->create(['title' => 'Soprano']);

        $singer = Membership::factory()->create();
        $singer->user->update(['first_name' => 'Alice', 'last_name' => 'Singer']);

        // Singer is in A and B, but NOT C
        Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'voice_part_id' => $soprano->id,
            'ensemble_id' => $ensembleA->id,
        ]);

        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
        ]);

        $mailable = new AttendanceReport($event);
        $html = $mailable->render();

        // This singer is NOT relevant to the event (not in Choir C)
        // BUT because we used relevant_memberships() which might include them if ensembles is empty, 
        // OR if they manually recorded attendance.
        // Wait, relevant_memberships() FOR an event with ensembles ONLY includes members of those ensembles.
        // So Alice shouldn't even be in $absent_singers if she's not in Choir C.
        
        // Let's verify Alice is NOT in the email
        $this->assertStringNotContainsString('Alice Singer', $html);
    }

    public function test_it_handles_singer_with_multiple_relevant_enrolments_correctly(): void
    {
        $event = Event::factory()->create(['title' => 'Event with Choir A and B']);
        $ensembleA = Ensemble::factory()->create(['name' => 'Choir A']);
        $ensembleB = Ensemble::factory()->create(['name' => 'Choir B']);
        $event->ensembles()->attach([$ensembleA->id, $ensembleB->id]);

        $soprano = VoicePart::factory()->create(['title' => 'Soprano']);
        $alto = VoicePart::factory()->create(['title' => 'Alto']);

        $singer = Membership::factory()->create();
        $singer->user->update(['first_name' => 'Alice', 'last_name' => 'Singer']);

        // Relevant in A as Soprano
        Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'voice_part_id' => $soprano->id,
            'ensemble_id' => $ensembleA->id,
        ]);

        // Relevant in B as Alto
        Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'voice_part_id' => $alto->id,
            'ensemble_id' => $ensembleB->id,
        ]);

        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
        ]);

        $mailable = new AttendanceReport($event);
        $html = $mailable->render();

        $this->assertStringContainsString('Choir A', $html);
        $this->assertStringContainsString('Soprano', $html);
        $this->assertStringContainsString('Choir B', $html);
        $this->assertStringContainsString('Alto', $html);
        $this->assertStringContainsString('Alice Singer', $html);
        $this->assertStringNotContainsString('No Part', $html);
    }
}
