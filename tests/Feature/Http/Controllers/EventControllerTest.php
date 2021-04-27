<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\EventController
 */
class EventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

        $response = $this->get(the_tenant_route('events.create'));

        $response->assertOk();
        $response->assertViewIs('events.create');
        $response->assertViewHas('types');
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

	    $event = Event::factory()->create();

        $response = $this->delete(the_tenant_route('events.destroy', [$event]));

        $this->assertSoftDeleted($event);
        $response->assertRedirect(the_tenant_route('events.index'));
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

	    $event = Event::factory()->create();

        $response = $this->get(the_tenant_route('events.edit', [$event]));

        $response->assertOk();
        $response->assertViewIs('events.edit');
        $response->assertViewHas('event');
        $response->assertViewHas('types');
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

	    $response = $this->get(the_tenant_route('events.index'));

        $response->assertOk();
        $response->assertViewIs('events.index');
        $response->assertViewHas('all_events');
        $response->assertViewHas('upcoming_events');
        $response->assertViewHas('past_events');
        $response->assertViewHas('filters');
        $response->assertViewHas('sorts');
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

        $response = $this->get(the_tenant_route('events.show', [$event]));

        $response->assertOk();
        $response->assertViewIs('events.show');
        $response->assertViewHas('event');
        $response->assertViewHas('my_attendance');
        $response->assertViewHas('singers_rsvp_yes_count');
        $response->assertViewHas('singers_rsvp_no_count');
        $response->assertViewHas('singers_rsvp_missing_count');
        $response->assertViewHas('voice_parts_rsvp_yes_count');
        $response->assertViewHas('singers_attendance_present');
        $response->assertViewHas('singers_attendance_absent');
        $response->assertViewHas('singers_attendance_absent_apology');
        $response->assertViewHas('singers_attendance_missing');
        $response->assertViewHas('voice_parts_attendance');
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function store_redirects_to_show($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

	    $data = $getData();
        $response = $this->post(the_tenant_route('events.store'), $data);

        $response->assertSessionHasNoErrors();

	    $date_format = 'Y-m-d H:i:s';
        $this->assertDatabaseHas('events', [
        	'title'                 => $data['title'],
	        'call_time'             => tz_from_tenant_to_utc($data['call_time'])->format($date_format),
	        'start_date'            => tz_from_tenant_to_utc($data['start_date'])->format($date_format),
	        'end_date'              => tz_from_tenant_to_utc($data['end_date'])->format($date_format),
	        'location_name'         => $data['location_name'],
	        'location_address'      => $data['location_address'],
	        'description'           => $data['description'],
	        'type_id'               => $data['type'],
        ]);

        $event = Event::firstWhere('title', $data['title']);
        $response->assertRedirect(the_tenant_route('events.show', [$event]));
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function update_redirects_to_show($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

	    $data = $getData();
        $response = $this->put(the_tenant_route('events.update', [$event]), $data);

	    $response->assertSessionHasNoErrors();

	    $date_format = 'Y-m-d H:i:s';
	    $this->assertDatabaseHas('events', [
		    'title'                 => $data['title'],
		    'call_time'             => tz_from_tenant_to_utc($data['call_time'])->format($date_format),
		    'start_date'            => tz_from_tenant_to_utc($data['start_date'])->format($date_format),
		    'end_date'              => tz_from_tenant_to_utc($data['end_date'])->format($date_format),
		    'location_name'         => $data['location_name'],
		    'location_address'      => $data['location_address'],
		    'description'           => $data['description'],
		    'type_id'               => $data['type'],
	    ]);
        $response->assertRedirect(the_tenant_route('events.show', [$event]));
    }

	public function eventProvider(): array
	{
		return [
			[
				function() {
					$this->setUpFaker();

					$date_format = 'Y-m-d H:i:s';
					$call_time = Carbon::instance( $this->faker->dateTimeThisYear() );
					$start_time = (clone $call_time)->addHour();
					$end_time = (clone $start_time)->addHours(2);
					return [
						'title'                 => $this->faker->sentence(6, true),
						'call_time'             => $call_time->format($date_format),
						'start_date'            => $start_time->format($date_format),
						'end_date'              => $end_time->format($date_format),
						'location_name'         => $this->faker->sentence(3, true),
						'location_address'      => $this->faker->address, // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
						'description'           => $this->faker->optional()->sentence,
						'type'                  => EventType::where('title', 'Rehearsal')->value('id'),
						'created_at'            => now(),
						'updated_at'            => now(),
					];
				}
			]
		];
	}
}
