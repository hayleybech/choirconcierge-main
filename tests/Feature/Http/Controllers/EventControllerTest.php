<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use App\Notifications\EventCreated;
use App\Notifications\EventUpdated;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Notification;
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
	    Notification::fake();
	    $this->actingAs($this->createUserWithRole('Events Team'));

	    ['request' => $request_data, 'saved' => $saved_data] = $getData();
	    $response = $this->post(the_tenant_route('events.store'), $request_data);

	    $response->assertSessionHasNoErrors();
	    $this->assertDatabaseHas('events', $saved_data);
	    Notification::assertNothingSent();

        $event = Event::firstWhere('title', $saved_data['title']);
        $response->assertRedirect(the_tenant_route('events.show', [$event]));
    }

	/**
	 * @test
	 * @dataProvider eventProvider
	 */
	public function store_sends_notification($getData): void
	{
		Notification::fake();
		$this->actingAs($this->createUserWithRole('Events Team'));

		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$request_data['send_notification'] = true;
		$response = $this->post(the_tenant_route('events.store'), $request_data);

		$response->assertSessionHasNoErrors();

		$this->assertDatabaseHas('events', ['title' => $saved_data['title']]);

		Notification::assertSentTo(auth()->user(), EventCreated::class);
	}

	/**
	 * @test
	 * @dataProvider eventProvider
	 * @todo simplify data setup
	 */
	public function store_creates_repeat_children($getData): void
	{
		$this->actingAs($this->createUserWithRole('Events Team'));

		$date_format = 'Y-m-d H:i:s';

		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$total_repeats = $this->faker->numberBetween(2, 20);
		$repeat_unit = $this->faker->randomElement(['days', 'weeks', 'months']);
		$request_data = array_merge($request_data, [
			'is_repeating'          => true,
			'repeat_frequency_unit' => $repeat_unit,
			'repeat_until'          => Carbon::create($request_data['call_time'])
				->add($total_repeats.' '.$repeat_unit)
				->format($date_format),
		]);

		$response = $this->post(the_tenant_route('events.store'), $request_data);
		$response->assertSessionHasNoErrors();
		
		$saved_data = array_merge($request_data, [
			'repeat_until'          => tz_from_tenant_to_utc($request_data['repeat_until'])->format($date_format),
		]);
		
		// Parent
		$this->assertDatabaseHas('events', array_merge($saved_data, [
			'call_time'             => tz_from_tenant_to_utc($request_data['call_time'])->format($date_format),
			'start_date'            => tz_from_tenant_to_utc($request_data['start_date'])->format($date_format),
			'end_date'              => tz_from_tenant_to_utc($request_data['end_date'])->format($date_format),
		]));

		// Total Children
		$this->assertDatabaseCount('events', 1 + $total_repeats);

		// Check child 1
		$this->assertDatabaseHas('events', array_merge($saved_data, [

			'call_time'             => tz_from_tenant_to_utc($saved_data['call_time'])
				->clone()
				->add('1 '.$repeat_unit)
				->format($date_format),
			'start_date'            => tz_from_tenant_to_utc($saved_data['start_date'])
				->clone()
				->add('1 '.$repeat_unit)
				->format($date_format),
			'end_date'              => tz_from_tenant_to_utc($saved_data['end_date'])
				->clone()
				->add('1 '.$repeat_unit)
				->format($date_format),
		]));

		// Check child 2
		$this->assertDatabaseHas('events', array_merge($saved_data, [
			'call_time'             => tz_from_tenant_to_utc($saved_data['call_time'])
				->clone()
				->add('2 '.$repeat_unit)
				->format($date_format),
			'start_date'            => tz_from_tenant_to_utc($saved_data['start_date'])
				->clone()
				->add('2 '.$repeat_unit)
				->format($date_format),
			'end_date'              => tz_from_tenant_to_utc($saved_data['end_date'])
				->clone()
				->add('2 '.$repeat_unit)
				->format($date_format),
		]));

		$event = Event::firstWhere('title', $request_data['title']);
		$response->assertRedirect(the_tenant_route('events.show', [$event]));
	}

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function update_redirects_to_show($getData): void
    {
	    Notification::fake();
	    $this->actingAs($this->createUserWithRole('Events Team'));

	    $event = Event::factory()->create();

	    ['request' => $request_data, 'saved' => $saved_data] = $getData();
	    $response = $this->put(the_tenant_route('events.update', [$event]), $request_data);

	    $response->assertSessionHasNoErrors();
	    $this->assertDatabaseHas('events', $saved_data);
	    $response->assertRedirect(the_tenant_route('events.show', [$event]));
	    Notification::assertNothingSent();
    }

	/**
	 * @test
	 * @dataProvider eventProvider
	 */
	public function update_for_single_updates_one($getData): void
	{
		Notification::fake();
		$this->actingAs($this->createUserWithRole('Events Team'));

		// Initial state
		$parent = Event::factory()
			->repeating()
			->create();
		$target = $parent;
		$next_sibling = $target->nextRepeat();

		// Make the update request
		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$request_data['edit_mode'] = 'single';
		$response = $this->put(the_tenant_route('events.update', [$target]), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target updated
		$this->assertDatabaseHas('events', $saved_data);
		$target->refresh();
		self::assertFalse($target->is_repeating);

		// Assert siblings not changed
		$this->assertDatabaseMissing('events', [
			'id'    => $next_sibling->id,
			'title' => $saved_data['title'],
		]);

		$response->assertRedirect(the_tenant_route('events.show', [$target]));
		Notification::assertNothingSent();
	}

	/**
	 * @test
	 * @dataProvider repeatingEventProvider
	 */
	public function update_for_all_updates_all($getData): void
	{
		Notification::fake();
		$this->actingAs($this->createUserWithRole('Events Team'));

		// Create event. Skip factory so we can re-use the request data
		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$response = $this->post(the_tenant_route('events.store'), $request_data);
		$response->assertSessionHasNoErrors();

		// Fetch the models we're testing
		$parent = Event::firstWhere('title', $request_data['title']);
		$first_child = $parent->repeat_children->first();
		$last_child = $parent->repeat_children->last();

		// Note: Dates aren't changing for this test.
		$request_data['edit_mode'] = 'all';
		$request_data['title'] = 'New title';
		$saved_data['title'] = 'New title';

		// Make the update request
		$response = $this->put(the_tenant_route('events.update', [$parent]), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target updated
		$this->assertDatabaseHas('events', $saved_data);
		$parent->refresh();

		// Assert siblings changed but not regenerated
		$this->assertDatabaseHas('events', [
			'id'    => $first_child->id,
			'title' => $saved_data['title'],
		]);
		$this->assertDatabaseHas('events', [
			'id'    => $last_child->id,
			'title' => $saved_data['title'],
		]);

		$response->assertRedirect(the_tenant_route('events.show', [$parent]));
		Notification::assertNothingSent();
	}

	/**
	 * @test
	 * @dataProvider repeatingEventProvider
	 * @todo Soft delete???
	 */
	public function update_for_all_regenerates_all_when_dirty($getData): void
	{
		Notification::fake();
		$this->actingAs($this->createUserWithRole('Events Team'));

		// Initial state
		$parent = Event::factory()
			->repeating()
			->create();
		$target = $parent;
		$first_child = $parent->repeat_children->first();
		$last_child = $parent->repeat_children->last();

		// Make the update request.
		// Note: We specifically want to change the event dates.
		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$request_data['edit_mode'] = 'all';
		$response = $this->put(the_tenant_route('events.update', [$target]), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target updated
		$this->assertDatabaseHas('events', $saved_data);

		// Assert siblings regenerated
		$this->assertSoftDeleted($first_child);
		$this->assertSoftDeleted($last_child);
		$target->refresh();
		$this->assertDatabaseHas('events', ['repeat_parent_id' => $target->id]);
		self::assertGreaterThan(0, $target->repeat_children->count());

		$response->assertRedirect(the_tenant_route('events.show', [$target]));
		Notification::assertNothingSent();
	}

	/**
	 * @test
	 * @dataProvider repeatingEventProvider
	 */
	public function update_for_following_updates_some($getData): void
	{
		Notification::fake();
		$this->actingAs($this->createUserWithRole('Events Team'));

		// Create event. Skip factory so we can re-use the request data
		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$response = $this->post(the_tenant_route('events.store'), $request_data);
		$response->assertSessionHasNoErrors();

		// Fetch the models we're testing
		$parent = Event::firstWhere('title', $request_data['title']);

		$target = $parent->repeat_children[round($parent->repeat_children->count() / 2)];
		$prev_sibling = $target->prevRepeat();
		$next_sibling = $target->nextRepeat();
		$last_sibling = $parent->repeat_children->last();

		// Make the update request
		$date_format = 'Y-m-d H:i:s';
		$request_data = array_merge($request_data, [
			'edit_mode' => 'following',
			'title'     => 'New title',

			'call_time' => $target->call_time->format($date_format),
			'start_date'=> $target->start_date->format($date_format),
			'end_date'  => $target->end_date->format($date_format),
		]);
		$saved_data['title'] = 'New title';
		$response = $this->put(the_tenant_route('events.update', [$target]), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target and siblings updated but not regenerated
		$this->assertDatabaseHas('events', [
			'id'    => $target->id,
			'title' => $saved_data['title'],
		]);
		$this->assertDatabaseHas('events', [
			'id'                => $next_sibling->id,
			'title'             => $saved_data['title'],
			'repeat_parent_id'  => $target->id,
			'deleted_at'        => null,
		]);
		$this->assertDatabaseHas('events', [
			'id'                => $last_sibling->id,
			'title'             => $saved_data['title'],
			'repeat_parent_id'  => $target->id,
			'deleted_at'        => null,
		]);

		// Assert earlier events intact
		$this->assertNotSoftDeleted($parent);
		$this->assertNotSoftDeleted($prev_sibling);

		// Assert series updated
		$parent->refresh();
		self::assertEquals($prev_sibling->call_time, $parent->repeat_until);

		$response->assertRedirect(the_tenant_route('events.show', [$target]));
		Notification::assertNothingSent();
	}

	/**
	 * @test
	 * @dataProvider repeatingEventProvider
	 */
	public function update_for_following_regenerates_some_when_dirty($getData): void
	{
		Notification::fake();
		$this->actingAs($this->createUserWithRole('Events Team'));

		// Initial state
		$parent = Event::factory()
			->repeating()
			->create();
		$target = $parent->repeat_children[round($parent->repeat_children->count() / 2)];
		$prev_sibling = $target->prevRepeat();
		$next_sibling = $target->nextRepeat();
		$last_sibling = $parent->repeat_children->last();

		// Make the update request
		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$request_data['edit_mode'] = 'following';
		$response = $this->put(the_tenant_route('events.update', [$target]), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target updated
		$this->assertDatabaseHas('events', [
			'id'    => $target->id,
			'title' => $saved_data['title'],
		]);

		// Assert following siblings regenerated
		$target->refresh();
		$this->assertSoftDeleted($next_sibling);
		$this->assertSoftDeleted($last_sibling);
		$this->assertDatabaseHas('events', ['repeat_parent_id' => $target->id]);
		self::assertGreaterThan(0, $target->repeat_children->count());

		// Assert earlier events intact
		$this->assertNotSoftDeleted($parent);
		$this->assertNotSoftDeleted($prev_sibling);

		// Assert series updated
		$parent->refresh();
		self::assertEquals($prev_sibling->call_time, $parent->repeat_until);

		$response->assertRedirect(the_tenant_route('events.show', [$target]));
		Notification::assertNothingSent();
	}

	/**
	 * @test
	 * @dataProvider eventProvider
	 */
	public function update_sends_notification($getData): void
	{
    	Notification::fake();
	    $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$request_data['send_notification'] = true;
        $response = $this->put(the_tenant_route('events.update', [$event]), $request_data);

	    $response->assertSessionHasNoErrors();
	    $this->assertDatabaseHas('events', ['title' => $request_data['title']]);
	    Notification::assertSentTo(auth()->user(), EventUpdated::class);
    }

	public function update_single_doesnt_change_children(): void
	{
		self::markTestIncomplete('WIP');
	}

	public function update_all_changes_childrens(): void
    {
		self::markTestIncomplete('WIP');
    }

    public function update_following_changes_children(): void
    {
    	self::markTestIncomplete('WIP');
    }

	public function eventProvider(): array
	{
		return [
			'randomised' => [
				function() {
					$this->setUpFaker();

					$date_format = 'Y-m-d H:i:s';
					$call_time = Carbon::instance( $this->faker->dateTimeBetween('now', '+1 year') );
					$start_time = (clone $call_time)->addHour();
					$end_time = (clone $start_time)->addHours(2);

					$request_data = [
						'title'                 => $this->faker->sentence(6, true),
						'call_time'             => $call_time->format($date_format),
						'start_date'            => $start_time->format($date_format),
						'end_date'              => $end_time->format($date_format),
						'location_name'         => $this->faker->sentence(3, true),
						'location_address'      => $this->faker->address(), // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
						'description'           => $this->faker->optional()->sentence(),
						'type_id'               => EventType::where('title', 'Rehearsal')->value('id'),
					];

					return [
						'request' => $request_data,
						'saved' => [
							'title'                 => $request_data['title'],
							'call_time'             => tz_from_tenant_to_utc($request_data['call_time'])->format($date_format),
							'start_date'            => tz_from_tenant_to_utc($request_data['start_date'])->format($date_format),
							'end_date'              => tz_from_tenant_to_utc($request_data['end_date'])->format($date_format),
							'location_name'         => $request_data['location_name'],
							'location_address'      => $request_data['location_address'],
							'description'           => $request_data['description'],
							'type_id'               => $request_data['type_id'],
						],
					];
				}
			]
		];
	}

	public function repeatingEventProvider(): array
	{
		return [
			[
				function() {
					$this->setUpFaker();

					$date_format = 'Y-m-d H:i:s';
					$call_time = Carbon::instance( $this->faker->dateTimeBetween('now', '+1 year') );
					$start_time = (clone $call_time)->addHour();
					$end_time = (clone $start_time)->addHours(2);

					$total_repeats = $this->faker->numberBetween(4, 20);
					$repeat_unit = $this->faker->randomElement(['days', 'weeks', 'months']);

					$request_data = [
						'title'                 => $this->faker->sentence(6, true),
						'call_time'             => $call_time->format($date_format),
						'start_date'            => $start_time->format($date_format),
						'end_date'              => $end_time->format($date_format),
						'location_name'         => $this->faker->sentence(3, true),
						'location_address'      => $this->faker->address(), // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
						'description'           => $this->faker->optional()->sentence(),
						'type_id'               => EventType::where('title', 'Rehearsal')->value('id'),

						'is_repeating'          => true,
						'repeat_frequency_unit' => $repeat_unit,
						'repeat_until'          => $call_time->clone()
							->add($total_repeats.' '.$repeat_unit)
							->format($date_format),
					];
					return [
						'request' => $request_data,
						'saved' => [
							'title'                 => $request_data['title'],
							'call_time'             => tz_from_tenant_to_utc($request_data['call_time'])->format($date_format),
							'start_date'            => tz_from_tenant_to_utc($request_data['start_date'])->format($date_format),
							'end_date'              => tz_from_tenant_to_utc($request_data['end_date'])->format($date_format),
							'location_name'         => $request_data['location_name'],
							'location_address'      => $request_data['location_address'],
							'description'           => $request_data['description'],
							'type_id'               => $request_data['type_id'],

							'repeat_until'          => tz_from_tenant_to_utc($request_data['repeat_until']),
						],
					];
				}
			]
		];
	}
}
