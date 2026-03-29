<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EventCheckInControllerTest extends TestCase
{
    public function test_it_can_check_in_to_an_event()
    {
        $user = User::factory()->create();
        $membership = Membership::factory()->for($user)->create();
        $event = Event::factory()->create([
            'start_date' => now()->startOfDay(),
            'end_date' => now()->endOfDay(),
            'call_time' => now()->subMinutes(10),
        ]);

        $url = URL::temporarySignedRoute('events.check-ins.store', now()->addMinutes(5), [
            'tenant' => 'phpunit',
            'event' => $event->id
        ]);

        // Remove the slash between tenant and events to see if it makes a difference, 
        // but normally Laravel handles it.
        // Let's try to simulate what could cause a 404.
        // If 'event' is missing from the URL params?
        
        $response = $this->actingAs($user)
            ->post($url, [
                'event_id' => $event->id
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'membership_id' => $membership->id,
            'event_id' => $event->id,
            'source' => 'qr-code',
        ]);
    }

    public function test_it_falls_back_to_parent_if_repeating_event_has_no_upcoming_children()
    {
        $user = User::factory()->create();
        $membership = Membership::factory()->for($user)->create();
        
        $event = Event::factory()->create([
            'is_repeating' => false,
            'start_date' => now()->startOfDay(),
            'end_date' => now()->endOfDay(),
            'call_time' => now()->subMinutes(10),
        ]);
        
        $event->update(['is_repeating' => true]);

        $url = URL::temporarySignedRoute('events.check-ins.store', now()->addMinutes(5), [
            'tenant' => 'phpunit',
            'event' => $event->id
        ]);

        $response = $this->actingAs($user)
            ->post($url, [
                'event_id' => $event->id
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'membership_id' => $membership->id,
            'event_id' => $event->id,
            'source' => 'qr-code',
        ]);
    }
}
