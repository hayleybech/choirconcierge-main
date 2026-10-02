<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\SingerStatus;
use App\Models\Membership;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LearningStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_music_team_member_can_view_a_learning_report_for_a_song(): void
    {
        $song = Song::factory()->create();
        User::factory()
            ->count(20)
            ->has(Membership::factory()->hasAttached(
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
                ->has('allSingers')
                ->has('pagination')
                ->has('voiceParts')
            );
    }

    public function test_index_can_filter_by_name(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();
        $singer1 = Membership::factory()->hasAttached($song, ['status' => 'not-started'])->create();
        $singer1->user->update(['first_name' => 'John', 'last_name' => 'Doe']);
        $singer2 = Membership::factory()->hasAttached($song, ['status' => 'not-started'])->create();
        $singer2->user->update(['first_name' => 'Jane', 'last_name' => 'Smith']);

        $this->get(the_tenant_route('songs.singers.index', ['song' => $song, 'filter[user.name]' => 'John']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers', 1)
                ->where('allSingers.0.user.name', 'John Doe')
            );
    }

    public function test_index_can_filter_by_learning_status(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();
        $singer1 = Membership::factory()->hasAttached($song, ['status' => 'performance-ready'])->create();
        $singer2 = Membership::factory()->hasAttached($song, ['status' => 'not-started'])->create();

        $this->get(the_tenant_route('songs.singers.index', ['song' => $song, 'filter[learning.status]' => 'performance-ready']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers', 1)
                ->where('allSingers.0.id', $singer1->id)
            );
    }

    public function test_index_can_filter_by_member_status(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $status1 = SingerStatus::MEMBERS->value;
        $status2 = SingerStatus::PROSPECTS->value;

        $song = Song::factory()->create();
        $singer1 = Membership::factory()->hasAttached($song, ['status' => 'not-started'])->create();
        $singer1->statuses()->create(['status' => $status1]);
        $singer2 = Membership::factory()->hasAttached($song, ['status' => 'not-started'])->create();
        $singer2->statuses()->create(['status' => $status2]);

        $this->get(the_tenant_route('songs.singers.index', ['song' => $song, 'filter[status.id]' => $status2]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers', 1)
                ->where('allSingers.0.id', $singer2->id)
            );
    }

    public function test_index_can_sort_by_name(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();
        $singer1 = Membership::factory()->hasAttached($song, ['status' => 'not-started'])->create();
        $singer1->user->update(['first_name' => 'Zebra', 'last_name' => 'Last']);
        $singer2 = Membership::factory()->hasAttached($song, ['status' => 'not-started'])->create();
        $singer2->user->update(['first_name' => 'Apple', 'last_name' => 'First']);

        $this->get(the_tenant_route('songs.singers.index', ['song' => $song, 'sort' => 'full-name']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($singer1, $singer2) {
                    $ids = collect($singers)->pluck('id');
                    $pos1 = $ids->search($singer1->id);
                    $pos2 = $ids->search($singer2->id);
                    return $pos2 < $pos1; // Apple (singer2) before Zebra (singer1)
                })
            );

        $this->get(the_tenant_route('songs.singers.index', ['song' => $song, 'sort' => '-full-name']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($singer1, $singer2) {
                    $ids = collect($singers)->pluck('id');
                    $pos1 = $ids->search($singer1->id);
                    $pos2 = $ids->search($singer2->id);
                    return $pos1 < $pos2; // Zebra (singer1) before Apple (singer2)
                })
            );
    }

    public function test_index_can_sort_by_learning_status(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();
        $singer1 = Membership::factory()->hasAttached($song, ['status' => 'performance-ready'])->create();
        $singer2 = Membership::factory()->hasAttached($song, ['status' => 'not-started'])->create();

        $this->get(the_tenant_route('songs.singers.index', ['song' => $song, 'sort' => 'learning-status']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($singer1, $singer2) {
                    $ids = collect($singers)->pluck('id');
                    $pos1 = $ids->search($singer1->id);
                    $pos2 = $ids->search($singer2->id);
                    return $pos1 < $pos2; // performance-ready (singer1) before not-started (singer2)
                })
            );

        $this->get(the_tenant_route('songs.singers.index', ['song' => $song, 'sort' => '-learning-status']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($singer1, $singer2) {
                    $ids = collect($singers)->pluck('id');
                    $pos1 = $ids->search($singer1->id);
                    $pos2 = $ids->search($singer2->id);
                    return $pos2 < $pos1; // not-started (singer2) before performance-ready (singer1)
                })
            );
    }

    public function test_a_music_team_member_can_update_the_learning_status_for_a_singer(): void
    {
        $song = Song::factory()->create();
        $user = User::factory()
            ->has(Membership::factory()->hasAttached(
                $song,
                ['status' => 'assessment-ready']
            ))
            ->create();

        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->put(the_tenant_route('songs.singers.update', [$song, $user->membership]), [
            'status' => 'performance-ready',
        ])
            ->assertRedirect(the_tenant_route('songs.singers.index', $song));

        $this->assertDatabaseHas('membership_song', [
            'song_id' => $song->id,
            'membership_id' => $user->membership->id,
            'status' => 'performance-ready',
        ]);
    }

    public function test_a_music_team_member_can_bulk_update_learning_statuses(): void
    {
        $song = Song::factory()->create();
        $users = User::factory()
            ->count(2)
            ->has(Membership::factory()->hasAttached($song, ['status' => 'assessment-ready']))
            ->create();

        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->post(the_tenant_route('songs.singers.bulk-update', [$song]), [
            'singer_ids' => $users->map(fn (User $user) => $user->membership->id)->all(),
            'status' => 'performance-ready',
        ])->assertRedirect(the_tenant_route('songs.singers.index', $song));

        foreach ($users as $user) {
            $this->assertDatabaseHas('membership_song', [
                'song_id' => $song->id,
                'membership_id' => $user->membership->id,
                'status' => 'performance-ready',
            ]);
        }
    }
}
