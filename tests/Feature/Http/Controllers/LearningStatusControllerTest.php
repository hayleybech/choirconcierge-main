<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Singer;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->get(the_tenant_route('songs.learning.index', $song))
            ->assertOk()
            ->assertViewHas('singers');
    }
}
