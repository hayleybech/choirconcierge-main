<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DeleteRecurringEventController
 */
class RecurringEventControllerTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/**
	 * @test
	 */
	public function destroy_for_single_deletes_one(): void
	{
        $this->markTestSkipped();
		$this->markAsRisky(); // @todo sometimes fails, but I don't know why

		$this->actingAs($this->createUserWithRole('Events Team'));

		$parent = Event::factory()
			->repeating()
			->create();

		$first_child = $parent->repeat_children->first();

		$response = $this->get(the_tenant_route('events.delete-recurring', [$parent, 'mode' => 'single']));
		$response->assertRedirect(the_tenant_route('events.index'));

		// Assert parent deleted
		$this->assertSoftDeleted($parent);

		// Assert children still exist
		$this->assertDatabaseHas('events', [
			'id' => $first_child->id,
			'repeat_until' => tz_from_tenant_to_utc($parent->repeat_until)->format('Y-m-d H:i:s'),
		]);

		// Assert parent ID changed
		$first_child->refresh();
		self::assertEquals($first_child->id, $first_child->repeat_parent_id);
	}

	/**
	 * @test
	 */
	public function destroy_for_all_deletes_all(): void
	{
        $this->markTestSkipped();
		$this->actingAs($this->createUserWithRole('Events Team'));

		$parent = Event::factory()
			->repeating()
			->create();

		$first_child = $parent->repeat_children->first();
		$last_child = $parent->repeat_children->last();

		$response = $this->get(the_tenant_route('events.delete-recurring', [$parent, 'mode' => 'all']));
		$response->assertRedirect(the_tenant_route('events.index'));

		// Assert parent deleted
		$this->assertSoftDeleted($parent);

		// Assert children deleted
		$this->assertSoftDeleted($first_child);
		$this->assertSoftDeleted($last_child);
	}

	/**
	 * @test
	 */
	public function destroy_for_following_deletes_some(): void
	{
        $this->markTestSkipped();
		$this->actingAs($this->createUserWithRole('Events Team'));

		$parent = Event::factory()
			->repeating()
			->create();

		$target = $parent->repeat_children[round($parent->repeat_children->count() / 2)];

		$prev_sibling = $target->prevRepeat();
		$next_sibling = $target->nextRepeat();
		$last_sibling = $parent->repeat_children->last();

		$response = $this->get(the_tenant_route('events.delete-recurring', [$target, 'mode' => 'following']));
		$response->assertRedirect(the_tenant_route('events.index'));

		// Assert target and following siblings deleted
		$this->assertSoftDeleted($target);
		$this->assertSoftDeleted($next_sibling);
		$this->assertSoftDeleted($last_sibling);

		// Assert earlier events intact
		$this->assertNotSoftDeleted($parent);
		$this->assertNotSoftDeleted($prev_sibling);

		// Assert series updated
		$parent->refresh();
		self::assertEquals($prev_sibling->call_time, $parent->repeat_until);
	}

	/**
	 * @test
	 * @dataProvider modeProvider
	 */
	public function edit_redirects_to_correct_edit_page($mode): void
	{
        $this->markTestSkipped();
		$this->actingAs($this->createUserWithRole('Events Team'));

		$event = Event::factory()
			->repeating()
			->create();
		self::assertGreaterThan(0, $event->repeat_children()->count());

		$response = $this->get(the_tenant_route('events.edit-recurring', [$event, 'mode' => $mode]));

		$edit_urls = [
			'single' => route('events.edit', ['event' => $event, 'mode' => $mode]),
			'following' => route('events.edit', ['event' => $event, 'mode' => $mode]),
			'all' => route('events.edit', ['event' => $event->repeat_parent, 'mode' => $mode]),
		];
		$response->assertRedirect($edit_urls[$mode]);
	}

	/**
	 * @test
	 * @dataProvider eventProvider
	 */
	public function update_for_single_updates_one($getData): void
	{
        $this->markTestSkipped();
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
		$response = $this->put(the_tenant_route('events.update-recurring', [$target, 'single']), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target updated
		$this->assertDatabaseHas('events', $saved_data);
		$target->refresh();
		self::assertFalse($target->is_repeating);

		// Assert siblings not changed
		$this->assertDatabaseMissing('events', [
			'id' => $next_sibling->id,
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
        $this->markTestSkipped();
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
		$request_data['title'] = 'New title';
		$saved_data['title'] = 'New title';

		// Make the update request
		$response = $this->put(the_tenant_route('events.update-recurring', [$parent, 'all']), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target updated
		$this->assertDatabaseHas('events', $saved_data);
		$parent->refresh();

		// Assert siblings changed but not regenerated
		$this->assertDatabaseHas('events', [
			'id' => $first_child->id,
			'title' => $saved_data['title'],
		]);
		$this->assertDatabaseHas('events', [
			'id' => $last_child->id,
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
        $this->markTestSkipped();
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
		$response = $this->put(the_tenant_route('events.update-recurring', [$target, 'all']), $request_data);
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
        $this->markTestSkipped();
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
			'title' => 'New title',

			'call_time' => $target->call_time->format($date_format),
			'start_date' => $target->start_date->format($date_format),
			'end_date' => $target->end_date->format($date_format),
		]);
		$saved_data['title'] = 'New title';
		$response = $this->put(the_tenant_route('events.update-recurring', [$target, 'following']), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target and siblings updated but not regenerated
		$this->assertDatabaseHas('events', [
			'id' => $target->id,
			'title' => $saved_data['title'],
		]);
		$this->assertDatabaseHas('events', [
			'id' => $next_sibling->id,
			'title' => $saved_data['title'],
			'repeat_parent_id' => $target->id,
			'deleted_at' => null,
		]);
		$this->assertDatabaseHas('events', [
			'id' => $last_sibling->id,
			'title' => $saved_data['title'],
			'repeat_parent_id' => $target->id,
			'deleted_at' => null,
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
        $this->markTestSkipped();
		Notification::fake();
		$this->actingAs($this->createUserWithRole('Events Team'));

		// Initial state
		$parent = Event::factory()
			->repeating()
			->create();
		$parent->load('repeat_children');
		$target = $parent->repeat_children[round($parent->repeat_children->count() / 2)];
		$target->load('repeat_children');
		$prev_sibling = $target->prevRepeat();
		$next_sibling = $target->nextRepeat();
		$last_sibling = $parent->repeat_children->last();

		// Make the update request
		['request' => $request_data, 'saved' => $saved_data] = $getData();
		$response = $this->put(the_tenant_route('events.update-recurring', [$target, 'following']), $request_data);
		$response->assertSessionHasNoErrors();

		// Assert target updated
		$this->assertDatabaseHas('events', [
			'id' => $target->id,
			'title' => $saved_data['title'],
		]);

		// Assert following siblings regenerated
		$target->refresh();
		$this->assertSoftDeleted($next_sibling);
		$this->assertSoftDeleted($last_sibling);
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

	public function modeProvider(): array
	{
		return [
			'single' => ['single'],
			'following' => ['following'],
			'all' => ['all'],
		];
	}

	public function eventProvider(): array
	{
		return [
			'randomised' => [
				function () {
					$this->setUpFaker();

					$date_format = 'Y-m-d H:i:s';
					$call_time = Carbon::instance($this->faker->dateTimeBetween('now', '+1 year'));
					$start_time = (clone $call_time)->addHour();
					$end_time = (clone $start_time)->addHours(2);

					$request_data = [
						'title' => $this->faker->sentence(6, true),
						'call_time' => $call_time->format($date_format),
						'start_date' => $start_time->format($date_format),
						'end_date' => $end_time->format($date_format),
						'location_name' => $this->faker->sentence(3, true),
						'location_address' => $this->faker->address(), // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
						'description' => $this->faker->optional()->sentence(),
						'type_id' => EventType::where('title', 'Rehearsal')->value('id'),
					];

					return [
						'request' => $request_data,
						'saved' => [
							'title' => $request_data['title'],
							'call_time' => tz_from_tenant_to_utc($request_data['call_time'])->format($date_format),
							'start_date' => tz_from_tenant_to_utc($request_data['start_date'])->format($date_format),
							'end_date' => tz_from_tenant_to_utc($request_data['end_date'])->format($date_format),
							'location_name' => $request_data['location_name'],
							'location_address' => $request_data['location_address'],
							'description' => $request_data['description'],
							'type_id' => $request_data['type_id'],
						],
					];
				},
			],
		];
	}

	public function repeatingEventProvider(): array
	{
		return [
			[
				function () {
					$this->setUpFaker();

					$date_format = 'Y-m-d H:i:s';
					$call_time = Carbon::instance($this->faker->dateTimeBetween('now', '+1 year'));
					$start_time = (clone $call_time)->addHour();
					$end_time = (clone $start_time)->addHours(2);

					$total_repeats = $this->faker->numberBetween(4, 20);
					$repeat_unit = $this->faker->randomElement(['days', 'weeks', 'months']);

					$request_data = [
						'title' => $this->faker->sentence(6, true),
						'call_time' => $call_time->format($date_format),
						'start_date' => $start_time->format($date_format),
						'end_date' => $end_time->format($date_format),
						'location_name' => $this->faker->sentence(3, true),
						'location_address' => $this->faker->address(), // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
						'description' => $this->faker->optional()->sentence(),
						'type_id' => EventType::where('title', 'Rehearsal')->value('id'),

						'is_repeating' => true,
						'repeat_frequency_unit' => $repeat_unit,
						'repeat_until' => $call_time
							->clone()
							->add($total_repeats . ' ' . $repeat_unit)
							->format($date_format),
					];
					return [
						'request' => $request_data,
						'saved' => [
							'title' => $request_data['title'],
							'call_time' => tz_from_tenant_to_utc($request_data['call_time'])->format($date_format),
							'start_date' => tz_from_tenant_to_utc($request_data['start_date'])->format($date_format),
							'end_date' => tz_from_tenant_to_utc($request_data['end_date'])->format($date_format),
							'location_name' => $request_data['location_name'],
							'location_address' => $request_data['location_address'],
							'description' => $request_data['description'],
							'type_id' => $request_data['type_id'],

							'repeat_until' => tz_from_tenant_to_utc($request_data['repeat_until']),
						],
					];
				},
			],
		];
	}
}
