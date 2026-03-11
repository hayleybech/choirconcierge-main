<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\Rsvp;
use App\Models\Role;
use App\Models\Membership;
use App\Models\SingerCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RsvpController
 */
class RsvpControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_destroy_redirects_back(): void
    {
        $this->actingAs(Membership::factory()->create()->user);

        $event = Event::factory()
            ->hasRsvps(['membership_id' => Auth::user()->membership->id])
            ->create();

        $response = $this->from(the_tenant_route('events.show', $event))->delete(
            the_tenant_route('events.rsvps.destroy', [$event, $event->rsvps->first()]),
        );

        $response->assertRedirect(the_tenant_route('events.show', $event));
        $this->assertModelMissing($event->rsvps->first());
    }

    public function test_store_redirects_back(): void
    {
        $this->actingAs(Membership::factory()->create()->user);

        $event = Event::factory()->create();

        $rsvp_response = $this->faker->randomElement(['yes', 'no']);
        $response = $this->from(the_tenant_route('events.show', $event))->post(
            the_tenant_route('events.rsvps.store', [$event]),
            [
                'rsvp_response' => $rsvp_response,
            ],
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('events.show', $event));
        $this->assertDatabaseHas('rsvps', [
            'response' => $rsvp_response,
            'event_id' => $event->id,
            'membership_id' => Auth::user()->membership->id,
        ]);
    }

    public function test_update_redirects_back(): void
    {
        $this->actingAs(Membership::factory()->create()->user);

        $event = Event::factory()
            ->hasRsvps(['membership_id' => Auth::user()->membership->id])
            ->create();

        $new_rsvp_response = $this->faker->randomElement(['yes', 'no']);
        $response = $this->from(the_tenant_route('events.show', $event))->put(
            the_tenant_route('events.rsvps.update', [$event, $event->rsvps->first()]),
            [
                'rsvp_response' => $new_rsvp_response,
            ],
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('events.show', $event));
        $this->assertDatabaseHas('rsvps', [
            'response' => $new_rsvp_response,
            'event_id' => $event->id,
            'membership_id' => Auth::user()->membership->id,
        ]);
    }

    public function test_update_changes_the_oldest(): void
    {
        $this->actingAs(Membership::factory()->create()->user);

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
        
        $response = $this->from(the_tenant_route('events.show', $event))->put(
            the_tenant_route('events.rsvps.update', [$event, $event->rsvps->first()]),
            [
                'rsvp_response' => 'yes',
            ],
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('events.show', $event));
        $this->assertDatabaseHas('rsvps', [
            'id' => $event->rsvps()->oldest()->first()->id,
            'response' => 'yes',
            'event_id' => $event->id,
            'membership_id' => Auth::user()->membership->id,
        ]);
    }

    public function test_index_returns_flat_list_of_singers(): void
    {
        $membership = Membership::factory()->create();
        $role = Role::create([
            'name' => 'Admin',
            'abilities' => ['rsvps_view'],
        ]);
        $membership->roles()->attach($role);
        $this->actingAs($membership->user);

        $event = Event::factory()->create();

        $response = $this->get(the_tenant_route('events.rsvps.index', $event));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Events/Rsvps/Index')
            ->has('event')
            ->has('allSingers')
        );
    }

    public function test_index_shows_single_row_per_singer_even_with_multiple_enrolments(): void
    {
        $membership = Membership::factory()->create();
        $role = Role::create([
            'name' => 'Admin',
            'abilities' => ['rsvps_view'],
        ]);
        $membership->roles()->attach($role);
        $this->actingAs($membership->user);

        $event = Event::factory()->create();
        
        $initialCount = Membership::active()->count();

        // Create two enrolments for the same membership
        \App\Models\Enrolment::factory()->create([
            'membership_id' => $membership->id,
            'ensemble_id' => \App\Models\Ensemble::factory()->create()->id,
        ]);
        \App\Models\Enrolment::factory()->create([
            'membership_id' => $membership->id,
            'ensemble_id' => \App\Models\Ensemble::factory()->create()->id,
        ]);

        $response = $this->get(the_tenant_route('events.rsvps.index', $event));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Events/Rsvps/Index')
            ->has('allSingers', $initialCount)
        );
    }

    public function test_index_filters_singers_by_event_ensembles(): void
    {
        $role = Role::create(['name' => 'Admin', 'abilities' => ['rsvps_view']]);
        $admin = Membership::factory()->create();
        $admin->roles()->attach($role);
        $this->actingAs($admin->user);

        $ensemble1 = \App\Models\Ensemble::factory()->create(['name' => 'Ensemble 1']);
        $ensemble2 = \App\Models\Ensemble::factory()->create(['name' => 'Ensemble 2']);

        $singerInEnsemble1 = Membership::factory()->create();
        \App\Models\Enrolment::factory()->create(['membership_id' => $singerInEnsemble1->id, 'ensemble_id' => $ensemble1->id]);

        $singerInEnsemble2 = Membership::factory()->create();
        \App\Models\Enrolment::factory()->create(['membership_id' => $singerInEnsemble2->id, 'ensemble_id' => $ensemble2->id]);

        $event = Event::factory()->create();
        $event->ensembles()->attach($ensemble1);

        $response = $this->get(the_tenant_route('events.rsvps.index', $event));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Events/Rsvps/Index')
            ->has('allSingers', 1) // Only singer in ensemble 1
            ->where('allSingers.0.user.name', $singerInEnsemble1->user->name)
        );
    }

    public function test_index_returns_total_ensembles_count(): void
    {
        $role = Role::create(['name' => 'Admin', 'abilities' => ['rsvps_view']]);
        $admin = Membership::factory()->create();
        $admin->roles()->attach($role);
        $this->actingAs($admin->user);

        $initialCount = \App\Models\Ensemble::count();
        \App\Models\Ensemble::factory()->count(3)->create();

        $event = Event::factory()->create();

        $response = $this->get(the_tenant_route('events.rsvps.index', $event));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('totalEnsemblesCount', $initialCount + 3)
        );
    }

    public function test_index_shows_multiple_enrolments_when_event_has_no_ensembles(): void
    {
        $role = Role::create(['name' => 'Admin', 'abilities' => ['rsvps_view']]);
        $admin = Membership::factory()->create();
        $admin->roles()->attach($role);
        $this->actingAs($admin->user);

        $initialCount = Membership::active()->count();

        $ensemble1 = \App\Models\Ensemble::factory()->create(['name' => 'Ensemble 1']);
        $ensemble2 = \App\Models\Ensemble::factory()->create(['name' => 'Ensemble 2']);

        $membership = Membership::factory()->create();
        $user = $membership->user;
        $user->first_name = 'ZZZZ_UNIQUE_SINGER_NAME';
        $user->save();
        
        \App\Models\Enrolment::factory()->create(['membership_id' => $membership->id, 'ensemble_id' => $ensemble1->id]);
        \App\Models\Enrolment::factory()->create(['membership_id' => $membership->id, 'ensemble_id' => $ensemble2->id]);

        $event = Event::factory()->create(); // No ensembles

        $response = $this->get(the_tenant_route('events.rsvps.index', $event));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Events/Rsvps/Index')
            ->has('allSingers', $initialCount + 1)
            ->where('allSingers.' . ($initialCount) . '.user.first_name', 'ZZZZ_UNIQUE_SINGER_NAME')
            ->where('allSingers.' . ($initialCount) . '.enrolments', fn($enrolments) => count($enrolments) === 2)
        );
    }

    public function test_event_show_page_counts_respect_ensemble_restriction(): void
    {
        $role = Role::create(['name' => 'Admin', 'abilities' => ['rsvps_view']]);
        $admin = Membership::factory()->create();
        $admin->roles()->attach($role);
        $this->actingAs($admin->user);

        $ensemble1 = \App\Models\Ensemble::factory()->create(['name' => 'Ensemble 1']);
        $ensemble2 = \App\Models\Ensemble::factory()->create(['name' => 'Ensemble 2']);

        // Singer 1 in Ensemble 1
        $singer1 = Membership::factory()->create();
        \App\Models\Enrolment::factory()->create(['membership_id' => $singer1->id, 'ensemble_id' => $ensemble1->id]);

        // Singer 2 in Ensemble 2
        $singer2 = Membership::factory()->create();
        \App\Models\Enrolment::factory()->create(['membership_id' => $singer2->id, 'ensemble_id' => $ensemble2->id]);

        $event = Event::factory()->create();
        $event->ensembles()->attach($ensemble1);

        // RSVP for singer 1 (Going)
        Rsvp::create([
            'membership_id' => $singer1->id,
            'event_id' => $event->id,
            'response' => 'yes',
        ]);

        // RSVP for singer 2 (Going - even though they are not in the ensemble)
        Rsvp::create([
            'membership_id' => $singer2->id,
            'event_id' => $event->id,
            'response' => 'yes',
        ]);

        $response = $this->get(the_tenant_route('events.show', $event));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Events/Show')
            ->where('rsvpCount.yes', 1) // Should only count singer 1
            ->where('rsvpCount.unknown', 0) // Admin is also a member, but if admin has no enrolment in ensemble1, they shouldn't be counted as missing either.
        );
    }

    public function test_cannot_rsvp_to_event_if_not_in_required_ensemble(): void
    {
        $ensemble1 = \App\Models\Ensemble::factory()->create(['name' => 'Ensemble 1']);
        $ensemble2 = \App\Models\Ensemble::factory()->create(['name' => 'Ensemble 2']);

        $singer2 = Membership::factory()->create();
        \App\Models\Enrolment::factory()->create(['membership_id' => $singer2->id, 'ensemble_id' => $ensemble2->id]);

        $event = Event::factory()->create();
        $event->ensembles()->attach($ensemble1);

        $this->actingAs($singer2->user);

        $response = $this->post(the_tenant_route('events.rsvps.store', $event), [
            'rsvp_response' => 'yes',
        ]);

        $response->assertForbidden();
    }

    public function test_index_can_filter_by_rsvp_response(): void
    {
        $role = Role::create(['name' => 'Admin', 'abilities' => ['rsvps_view']]);
        $admin = Membership::factory()->create();
        $admin->roles()->attach($role);
        $this->actingAs($admin->user);

        $event = Event::factory()->create();

        $singer1 = Membership::factory()->create();
        Rsvp::create(['membership_id' => $singer1->id, 'event_id' => $event->id, 'response' => 'yes']);

        $singer2 = Membership::factory()->create();
        Rsvp::create(['membership_id' => $singer2->id, 'event_id' => $event->id, 'response' => 'no']);

        $singer3 = Membership::factory()->create(); // No RSVP (unknown)

        // Filter for 'yes'
        $response = $this->get(the_tenant_route('events.rsvps.index', [
            'event' => $event->id,
            'filter' => ['rsvp.response' => 'yes'],
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('allSingers', 1)
            ->where('allSingers.0.id', $singer1->id)
        );

        // Filter for 'unknown'
        $response = $this->get(the_tenant_route('events.rsvps.index', [
            'event' => $event->id,
            'filter' => ['rsvp.response' => 'unknown'],
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('allSingers', fn($singers) => collect($singers)->pluck('id')->contains($singer3->id))
        );
    }

    public function test_index_can_sort_by_rsvp_response(): void
    {
        $role = Role::create(['name' => 'Admin', 'abilities' => ['rsvps_view']]);
        $admin = Membership::factory()->create();
        $admin->roles()->attach($role);
        $this->actingAs($admin->user);

        $event = Event::factory()->create();

        $singer1 = Membership::factory()->create();
        $singer1->user->first_name = 'A_Singer1';
        $singer1->user->save();
        Rsvp::create(['membership_id' => $singer1->id, 'event_id' => $event->id, 'response' => 'yes']);

        $singer2 = Membership::factory()->create();
        $singer2->user->first_name = 'B_Singer2';
        $singer2->user->save();
        Rsvp::create(['membership_id' => $singer2->id, 'event_id' => $event->id, 'response' => 'no']);

        $singer3 = Membership::factory()->create(); // unknown
        $singer3->user->first_name = 'C_Singer3';
        $singer3->user->save();

        // ASC: 'yes' (1), 'maybe' (2), 'no' (3), 'unknown' (4)
        $response = $this->get(the_tenant_route('events.rsvps.index', [
            'event' => $event->id,
            'sort' => 'rsvp-response,full-name',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('allSingers.0.id', $singer1->id) // 'yes'
            ->where('allSingers', fn($singers) => collect($singers)->pluck('id')->last() === $singer3->id || collect($singers)->pluck('id')->contains($singer3->id)) 
        );

        // DESC: 'unknown' (4), 'no' (3), 'maybe' (2), 'yes' (1)
        $response = $this->get(the_tenant_route('events.rsvps.index', [
            'event' => $event->id,
            'sort' => '-rsvp-response,full-name',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('allSingers', fn($singers) => in_array(collect($singers)->first()['id'], [$singer3->id, $admin->id])) // 'unknown' is first
            ->where('allSingers', fn($singers) => collect($singers)->last()['id'] === $singer1->id) // 'yes' is last
        );
    }

    public function test_index_can_sort_by_dietary_medical_presence(): void
    {
        $role = Role::create(['name' => 'Admin', 'abilities' => ['rsvps_view']]);
        $admin = Membership::factory()->create();
        $admin->roles()->attach($role);
        $this->actingAs($admin->user);

        $event = Event::factory()->create();

        // Singer 1: No dietary or medical
        $singer1 = Membership::factory()->create();
        $singer1->user->update([
            'dietary_requirements' => null,
            'medical_conditions' => '',
        ]);

        // Singer 2: Has dietary
        $singer2 = Membership::factory()->create();
        $singer2->user->update([
            'dietary_requirements' => 'Peanuts',
            'medical_conditions' => null,
        ]);

        // Singer 3: Has medical
        $singer3 = Membership::factory()->create();
        $singer3->user->update([
            'dietary_requirements' => null,
            'medical_conditions' => 'Asthma',
        ]);

        // ASC: Has value (0) first, then No value (1)
        $response = $this->get(the_tenant_route('events.rsvps.index', [
            'event' => $event->id,
            'sort' => 'dietary-medical,full-name',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('allSingers', function ($singers) use ($singer2, $singer3, $singer1) {
                $singers = collect($singers);
                $ids = $singers->pluck('id')->toArray();
                // The first two should be singer2 and singer3 (order by full-name)
                return in_array($singer2->id, array_slice($ids, 0, 3)) &&
                       in_array($singer3->id, array_slice($ids, 0, 3)) &&
                       $singers->contains('id', $singer1->id);
            })
        );

        // DESC: No value (1) first, then Has value (0)
        $response = $this->get(the_tenant_route('events.rsvps.index', [
            'event' => $event->id,
            'sort' => '-dietary-medical,full-name',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('allSingers', function ($singers) use ($singer2, $singer3, $singer1) {
                $singers = collect($singers);
                $ids = $singers->pluck('id')->toArray();
                // singer1 (and admin) should be at the beginning
                return $singers->contains('id', $singer1->id) &&
                       in_array($singer2->id, array_slice($ids, -3)) &&
                       in_array($singer3->id, array_slice($ids, -3));
            })
        );
    }
    public function test_index_can_filter_by_member_category(): void
    {
        $role = Role::create(['name' => 'Admin', 'abilities' => ['rsvps_view']]);
        $admin = Membership::factory()->create();
        $admin->roles()->attach($role);
        $this->actingAs($admin->user);

        $category1 = SingerCategory::factory()->create();
        $category2 = SingerCategory::factory()->create();

        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create(['singer_category_id' => $category1->id]);
        $singer2 = Membership::factory()->create(['singer_category_id' => $category2->id]);

        $this->get(the_tenant_route('events.rsvps.index', ['event' => $event, 'filter[category.id]' => $category1->id]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers', 1)
                ->where('allSingers.0.id', $singer1->id)
            );
    }
}
