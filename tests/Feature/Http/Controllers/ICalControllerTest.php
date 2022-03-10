<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ICalController
 */
class ICalControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_returns_an_ical_feed(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $events = Event::factory()
            ->count(10)
            ->create();

        $response = $this->get(the_tenant_route('events.feed'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/calendar; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="events-calendar.ics"');
        $response->assertHeader('charset', 'utf-8');
        $response->assertSeeText(['BEGIN:VCALENDAR', 'BEGIN:VEVENT']);
    }
}
