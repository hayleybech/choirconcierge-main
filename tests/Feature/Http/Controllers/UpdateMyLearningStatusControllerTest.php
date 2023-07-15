<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\Membership;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateMyLearningStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_singer_can_update_their_learning_status_for_a_song(): void
    {
        $song = Song::factory()->create();
        $user = User::factory()
            ->has(Membership::factory())
            ->create();
        $user->membership->roles()->attach([Role::where('name', 'User')->value('id')]);

        $this->actingAs($user);

        $response = $this->post(the_tenant_route('songs.my-learning.update', $song), [
            'status' => 'assessment-ready',
        ]);

        $this->assertDatabaseHas('membership_song', [
            'membership_id' => $user->membership->id,
            'song_id' => $song->id,
            'status' => 'assessment-ready',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('songs.show', [$song]));
    }

    /** @test */
    public function a_singer_can_reset_their_learning_status_for_a_song(): void
    {
        $song = Song::factory()->create();
        $user = User::factory()
            ->has(Membership::factory()->hasAttached(
                $song,
                ['status' => 'performance-ready']
            ))
            ->create();
        $user->membership->roles()->attach([Role::where('name', 'User')->value('id')]);

        $this->actingAs($user);

        $response = $this->post(the_tenant_route('songs.my-learning.update', $song), [
            'status' => 'assessment-ready',
        ]);

        $this->assertDatabaseHas('membership_song', [
            'membership_id' => $user->membership->id,
            'song_id' => $song->id,
            'status' => 'assessment-ready',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('songs.show', [$song]));
    }
}
