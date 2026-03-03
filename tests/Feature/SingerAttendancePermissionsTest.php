<?php

namespace Tests\Feature;

use App\Models\SingerCategory;
use App\Models\Membership;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia;

class SingerAttendancePermissionsTest extends TestCase
{
    /**
     * @test
     */
    public function a_singer_can_view_their_own_attendance_page(): void
    {
        $singer = Membership::factory()->create();
        $this->actingAs($singer->user);

        $this->get(the_tenant_route('singers.attendance', [$singer]))
            ->assertOk();
    }

    /**
     * @test
     */
    public function a_user_with_attendance_view_ability_can_view_any_singers_attendance_page(): void
    {
        $role = Role::factory()->create([
            'name' => 'Attendance Manager',
            'abilities' => ['attendances_view'],
        ]);
        $manager = Membership::factory()->create();
        $manager->roles()->attach($role);

        $singer = Membership::factory()->create();

        $this->actingAs($manager->user);

        $this->get(the_tenant_route('singers.attendance', [$singer]))
            ->assertOk();
    }

    /**
     * @test
     */
    public function an_admin_can_view_any_singers_attendance_page(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $singer = Membership::factory()->create();

        $this->actingAs($admin);

        $this->get(the_tenant_route('singers.attendance', [$singer]))
            ->assertOk();
    }

    /**
     * @test
     */
    public function a_user_cannot_view_another_singers_attendance_page(): void
    {
        $user1 = Membership::factory()->create();
        $user2 = Membership::factory()->create();

        $this->actingAs($user1->user);

        $this->get(the_tenant_route('singers.attendance', [$user2]))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function the_view_attendance_flag_is_passed_to_the_singer_profile_page(): void
    {
        $category = SingerCategory::where('name', 'Members')->firstOrCreate();
        $user1 = Membership::factory()->create(['singer_category_id' => $category->id]);
        $user2 = Membership::factory()->create(['singer_category_id' => $category->id]);

        // User1 viewing their own profile
        $this->actingAs($user1->user);
        $this->get(the_tenant_route('singers.show', [$user1]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('singer.can.view_attendance', true)
            );

        // A user with only singers_view can view another singer's profile but not their attendance
        $readerRole = Role::factory()->create([
            'name' => 'Reader',
            'abilities' => ['singers_view'],
        ]);
        
        $user1->roles()->syncWithoutDetaching([$readerRole->id]);
        $user1->unsetRelation('roles');
        
        $this->actingAs($user1->user);

        $user1->user->unsetRelation('membership');
        
        $this->get(the_tenant_route('singers.show', [$user2]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('singer.can.view_attendance', false)
            );

        // Admin viewing User2's profile (should be true)
        $admin = $this->createUserWithRole('Admin');
        $this->actingAs($admin);
        $this->get(the_tenant_route('singers.show', [$user2]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('singer.can.view_attendance', true)
            );
    }
}
