<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Singer;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LearningStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_music_team_member_can_view_a_learning_report_for_a_song(): void
    {
        $song = Song::factory()->create();
        User::factory()
            ->count(20)
            ->has(Singer::factory()->hasAttached(
                $song,
                ['status' => 'performance-ready']
            ))
            ->create();

        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('songs.singers.index', [$song]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Songs/Learning/Index')
                ->has('song')
                ->has('voice_parts')
            );
    }

    /** @test */
    public function a_music_team_member_can_update_the_learning_status_for_a_singer(): void
    {
        $song = Song::factory()->create();
        $user = User::factory()
            ->has(Singer::factory()->hasAttached(
                $song,
                ['status' => 'assessment-ready']
            ))
            ->create();

        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->put(the_tenant_route('songs.singers.update', [$song, $user->singer]), [
            'status' => 'performance-ready',
        ])
            ->assertRedirect(the_tenant_route('songs.singers.index', $song));

        $this->assertDatabaseHas('singer_song', [
            'song_id' => $song->id,
            'singer_id' => $user->singer->id,
            'status' => 'performance-ready',
        ]);
    }
}
