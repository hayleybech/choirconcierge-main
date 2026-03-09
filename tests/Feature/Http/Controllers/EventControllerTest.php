<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Ensemble;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Rsvp;
use App\Notifications\EventCreated;
use App\Notifications\EventUpdated;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia;
use Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\EventController
 */
class EventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $this->get(the_tenant_route('events.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Create')
                ->has('types')
                ->has('ensembles')
            );
    }

    public function test_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

        $this->delete(the_tenant_route('events.destroy', [$event]))
            ->assertRedirect(the_tenant_route('events.index'));

        $this->assertSoftDeleted($event);
    }

    public function test_edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

        $this->get(the_tenant_route('events.edit', [$event]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Edit')
                ->has('event')
                ->has('types')
                ->has('ensembles')
            );
    }

    public function test_index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $this->get(the_tenant_route('events.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Index')
                ->has('events')
                ->has('eventTypes')
                ->has('userEnsemblesCount')
                ->has('ensembles')
            );
    }

    public function test_user_can_view_event_in_their_ensemble(): void
    {
        $user = $this->createUserWithRole('User');
        $ensemble = Ensemble::factory()->create();
        $user->membership->enrolments()->create([
            'ensemble_id' => $ensemble->id,
            'voice_part_id' => \App\Models\VoicePart::factory()->create()->id,
        ]);

        $event = Event::factory()->create();
        $event->ensembles()->attach($ensemble);

        $this->actingAs($user);

        $this->get(the_tenant_route('events.show', [$event]))
            ->assertOk();
    }

    public function test_user_cannot_view_event_in_different_ensemble(): void
    {
        $user = $this->createUserWithRole('User');
        $userEnsemble = Ensemble::factory()->create();
        $user->membership->enrolments()->create([
            'ensemble_id' => $userEnsemble->id,
            'voice_part_id' => \App\Models\VoicePart::factory()->create()->id,
        ]);

        $otherEnsemble = Ensemble::factory()->create();
        $event = Event::factory()->create();
        $event->ensembles()->attach($otherEnsemble);

        $this->actingAs($user);

        $this->get(the_tenant_route('events.show', [$event]))
            ->assertForbidden();
    }

    public function test_music_team_can_view_any_ensemble_event(): void
    {
        $user = $this->createUserWithRole('Events Team');
        $ensemble = Ensemble::factory()->create();
        $event = Event::factory()->create();
        $event->ensembles()->attach($ensemble);

        $this->actingAs($user);

        $this->get(the_tenant_route('events.show', [$event]))
            ->assertOk();
    }

    public function test_index_filters_by_ensemble(): void
    {
        $user = $this->createUserWithRole('User');
        $ensemble = Ensemble::factory()->create();
        $user->membership->enrolments()->create([
            'ensemble_id' => $ensemble->id,
            'voice_part_id' => \App\Models\VoicePart::factory()->create()->id,
        ]);

        $eventInEnsemble = Event::factory()->create(['title' => 'In Ensemble', 'start_date' => now()->addDay()]);
        $eventInEnsemble->ensembles()->attach($ensemble);

        $eventNotInEnsemble = Event::factory()->create(['title' => 'Not In Ensemble', 'start_date' => now()->addDays(2)]);
        $otherEnsemble = Ensemble::factory()->create();
        $eventNotInEnsemble->ensembles()->attach($otherEnsemble);

        $eventNoEnsemble = Event::factory()->create(['title' => 'No Ensemble', 'start_date' => now()->addDays(3)]);

        $this->actingAs($user);

        // Test automatic filtering (user only sees what they have access to)
        $this->get(the_tenant_route('events.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('events', 2)
                ->where('events.0.title', 'In Ensemble')
                ->where('events.1.title', 'No Ensemble')
                ->has('ensembles', 1)
            );

        // Test explicit filter
        $this->get(the_tenant_route('events.index', ['filter' => ['ensembles.id' => [$ensemble->id]]]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('events', 1)
                ->where('events.0.title', 'In Ensemble')
            );
    }

    public function test_it_shows_the_oldest_rsvp_for_an_event(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

        Rsvp::factory()
            ->count(2)
            ->sequence(
                [
                    'response' => 'no',
                    'membership_id' => Auth::user()->membership->id,
                    'event_id' => $event->id,
                    'created_at' => now(),
                ],
                [
                    'response' => 'no',
                    'membership_id' => Auth::user()->membership->id,
                    'event_id' => $event->id,
                    'created_at' => now()->addMinute(),
                ],
            )
            ->create();

        $newestRsvp = $event->rsvps()->latest()->first();
        $newestRsvp->update(['response' => 'yes']);


        $this->get(the_tenant_route('events.show', [$event]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Show')
                ->where('event.my_rsvp.response',  'no')
            );
    }

    #[DataProvider('eventProvider')]
    public function test_store_redirects_to_show($getData): void
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

    #[DataProvider('eventProvider')]
    public function test_store_sends_notification($getData): void
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
     * @todo simplify data setup
     */
    #[DataProvider('eventProvider')]
    public function test_store_creates_repeat_children($getData): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $date_format = 'Y-m-d H:i:s';

        $faker = Factory::create();

        ['request' => $request_data, 'saved' => $saved_data] = $getData();
        $total_repeats = $faker->numberBetween(2, 20);
        $repeat_unit = $faker->randomElement(['days', 'weeks', 'months']);
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

    #[DataProvider('eventProvider')]
    public function test_update_redirects_to_show($getData): void
    {
        Notification::fake();
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();
        $ensemble = Ensemble::factory()->create();

        ['request' => $request_data, 'saved' => $saved_data] = $getData();
        $request_data['ensembles'] = [$ensemble->id];

        $response = $this->put(the_tenant_route('events.update', [$event]), $request_data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('events', $saved_data);
        $this->assertDatabaseHas('ensemble_event', [
            'event_id' => $event->id,
            'ensemble_id' => $ensemble->id,
        ]);
        $response->assertRedirect(the_tenant_route('events.show', [$event]));
        Notification::assertNothingSent();
    }

    #[DataProvider('eventProvider')]
    public function test_update_sends_notification($getData): void
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

    public function test_update_single_doesnt_change_children(): void
    {
        self::markTestIncomplete('WIP');
    }

    public function test_update_all_changes_childrens(): void
    {
        self::markTestIncomplete('WIP');
    }

    public function test_update_following_changes_children(): void
    {
        self::markTestIncomplete('WIP');
    }

    public static function eventProvider(): array
    {
        return [
            'randomised' => [
                function () {
                    $faker = Factory::create();

                    $date_format = 'Y-m-d H:i:s';
                    $call_time = Carbon::instance($faker->dateTimeBetween('now', '+1 year'));
                    $start_time = (clone $call_time)->addHour();
                    $end_time = (clone $start_time)->addHours(2);

                    $request_data = [
                        'title' => $faker->sentence(6, true),
                        'call_time' => $call_time->format($date_format),
                        'start_date' => $start_time->format($date_format),
                        'end_date' => $end_time->format($date_format),
                        'location_name' => $faker->sentence(3, true),
                        'location_address' => $faker->address(), // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
                        'description' => $faker->optional()->sentence(),
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

    public static function repeatingEventProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    $date_format = 'Y-m-d H:i:s';
                    $call_time = Carbon::instance($faker->dateTimeBetween('now', '+1 year'));
                    $start_time = (clone $call_time)->addHour();
                    $end_time = (clone $start_time)->addHours(2);

                    $total_repeats = $faker->numberBetween(4, 20);
                    $repeat_unit = $faker->randomElement(['days', 'weeks', 'months']);

                    $request_data = [
                        'title' => $faker->sentence(6, true),
                        'call_time' => $call_time->format($date_format),
                        'start_date' => $start_time->format($date_format),
                        'end_date' => $end_time->format($date_format),
                        'location_name' => $faker->sentence(3, true),
                        'location_address' => $faker->address(), // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
                        'description' => $faker->optional()->sentence(),
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
