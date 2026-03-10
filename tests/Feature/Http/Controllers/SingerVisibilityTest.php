<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Ensemble;
use App\Models\Membership;
use App\Models\Enrolment;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SingerVisibilityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_only_see_singers_in_the_same_ensemble_when_multiple_exist(): void
    {
        // 1. Setup: create two ensembles
        $ensembleA = Ensemble::factory()->create(['name' => 'Ensemble A']);
        $ensembleB = Ensemble::factory()->create(['name' => 'Ensemble B']);

        // 2. Create User (Singer) in Ensemble A with only 'User' role
        $userSinger = Membership::factory()->create();
        $userSinger->roles()->attach([Role::where('name', 'User')->valueOrFail('id')]);
        Enrolment::create([
            'membership_id' => $userSinger->id,
            'ensemble_id' => $ensembleA->id,
            'voice_part_id' => null,
        ]);
        $user = $userSinger->user;

        // 3. Create another Singer in Ensemble A
        $singerA = Membership::factory()->create();
        Enrolment::create([
            'membership_id' => $singerA->id,
            'ensemble_id' => $ensembleA->id,
            'voice_part_id' => null,
        ]);

        // 4. Create another Singer in Ensemble B
        $singerB = Membership::factory()->create();
        Enrolment::create([
            'membership_id' => $singerB->id,
            'ensemble_id' => $ensembleB->id,
            'voice_part_id' => null,
        ]);

        $this->actingAs($user);

        // 5. Index Request
        $this->get(the_tenant_route('singers.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Index')
                ->has('allSingers', 2)
                ->has('ensembles', 1)
            );

        // 6. Show Request for same ensemble
        $this->get(the_tenant_route('singers.show', [$singerA]))
            ->assertOk();

        // 7. Show Request for different ensemble
        $this->get(the_tenant_route('singers.show', [$singerB]))
            ->assertForbidden();
    }

    public function test_user_can_see_all_singers_if_only_one_ensemble_exists(): void
    {
        // 1. Setup: create only one ensemble
        Ensemble::query()->delete();
        $ensembleA = Ensemble::factory()->create(['name' => 'Ensemble A']);

        // 2. Create User (Singer) in Ensemble A with only 'User' role
        $userSinger = Membership::factory()->create();
        $userSinger->roles()->attach([Role::where('name', 'User')->valueOrFail('id')]);
        Enrolment::firstOrCreate([
            'membership_id' => $userSinger->id,
            'ensemble_id' => $ensembleA->id,
            'voice_part_id' => null,
        ]);
        $user = $userSinger->user;

        // 3. Create another Singer in Ensemble A
        $singerA = Membership::factory()->create();
        Enrolment::firstOrCreate([
            'membership_id' => $singerA->id,
            'ensemble_id' => $ensembleA->id,
            'voice_part_id' => null,
        ]);

        $this->actingAs($user);

        // 4. Index Request
        $this->get(the_tenant_route('singers.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Index')
                ->has('allSingers', Membership::count())
                ->has('ensembles', 1)
            );
    }

    public function test_user_can_see_all_singers_if_has_management_ability(): void
    {
        // 1. Setup: create two ensembles
        $ensembleA = Ensemble::factory()->create(['name' => 'Ensemble A']);
        $ensembleB = Ensemble::factory()->create(['name' => 'Ensemble B']);

        // 2. Create User (Singer) with 'Membership Team' role (has 'singers_update')
        $user = $this->createUserWithRole('Membership Team');
        $userSinger = $user->membership;
        Enrolment::create([
            'membership_id' => $userSinger->id,
            'ensemble_id' => $ensembleA->id,
            'voice_part_id' => null,
        ]);

        // 3. Create another Singer in Ensemble B
        $singerB = Membership::factory()->create();
        Enrolment::create([
            'membership_id' => $singerB->id,
            'ensemble_id' => $ensembleB->id,
            'voice_part_id' => null,
        ]);

        $this->actingAs($user);

        // 4. Index Request
        $this->get(the_tenant_route('singers.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Index')
                ->has('allSingers', Membership::count())
                ->has('ensembles', 2)
            );
    }
}
