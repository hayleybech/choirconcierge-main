<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Notifications\EventCreated;
use App\Notifications\EventUpdated;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
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

        $this->get(the_tenant_route('events.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Create')
                ->has('types')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

        $this->delete(the_tenant_route('events.destroy', [$event]))
            ->assertRedirect(the_tenant_route('events.index'));

        $this->assertSoftDeleted($event);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

        $this->get(the_tenant_route('events.edit', [$event]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Edit')
                ->has('event')
                ->has('types')
            );
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $this->get(the_tenant_route('events.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Index')
                ->has('events')
                ->has('eventTypes')
            );
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

        $this->get(the_tenant_route('events.show', [$event]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Show')
                ->has('event')
                ->has('rsvpCount')
                ->has('voicePartsRsvpCount')
                ->has('attendanceCount')
                ->has('voicePartsAttendanceCount')
                ->has('addToCalendarLinks')
            );
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
            'is_repeating' => true,
            'repeat_frequency_unit' => $repeat_unit,
            'repeat_until' => Carbon::create($request_data['call_time'])
                ->add($total_repeats.' '.$repeat_unit)
                ->format($date_format),
        ]);

        $response = $this->post(the_tenant_route('events.store'), $request_data);
        $response->assertSessionHasNoErrors();

        $saved_data = array_merge($request_data, [
            'repeat_until' => tz_from_tenant_to_utc($request_data['repeat_until'])->format($date_format),
        ]);

        // Parent
        $this->assertDatabaseHas(
            'events',
            array_merge($saved_data, [
                'call_time' => tz_from_tenant_to_utc($request_data['call_time'])->format($date_format),
                'start_date' => tz_from_tenant_to_utc($request_data['start_date'])->format($date_format),
                'end_date' => tz_from_tenant_to_utc($request_data['end_date'])->format($date_format),
            ]),
        );

        // Total Children
        $this->assertDatabaseCount('events', 1 + $total_repeats);

        // Check child 1
        $this->assertDatabaseHas(
            'events',
            array_merge($saved_data, [
                'call_time' => tz_from_tenant_to_utc($saved_data['call_time'])
                    ->clone()
                    ->add('1 '.$repeat_unit)
                    ->format($date_format),
                'start_date' => tz_from_tenant_to_utc($saved_data['start_date'])
                    ->clone()
                    ->add('1 '.$repeat_unit)
                    ->format($date_format),
                'end_date' => tz_from_tenant_to_utc($saved_data['end_date'])
                    ->clone()
                    ->add('1 '.$repeat_unit)
                    ->format($date_format),
            ]),
        );

        // Check child 2
        $this->assertDatabaseHas(
            'events',
            array_merge($saved_data, [
                'call_time' => tz_from_tenant_to_utc($saved_data['call_time'])
                    ->clone()
                    ->add('2 '.$repeat_unit)
                    ->format($date_format),
                'start_date' => tz_from_tenant_to_utc($saved_data['start_date'])
                    ->clone()
                    ->add('2 '.$repeat_unit)
                    ->format($date_format),
                'end_date' => tz_from_tenant_to_utc($saved_data['end_date'])
                    ->clone()
                    ->add('2 '.$repeat_unit)
                    ->format($date_format),
            ]),
        );

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
                            ->add($total_repeats.' '.$repeat_unit)
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
