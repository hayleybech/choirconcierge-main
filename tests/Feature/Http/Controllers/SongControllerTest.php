<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\Membership;
use App\Models\Song;
use App\Models\SongCategory;
use App\Models\SongStatus;
use App\Models\User;
use App\Notifications\SongUpdated;
use App\Notifications\SongUploaded;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SongController
 */
class SongControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('songs.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Songs/Create')
                ->has('categories')
                ->has('statuses')
                ->has('pitches')
            );
    }

    public function test_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

        $this->delete(the_tenant_route('songs.destroy', [$song]))
            ->assertRedirect(the_tenant_route('songs.index'));

        $this->assertSoftDeleted($song);
    }

    public function test_edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

        $this->get(the_tenant_route('songs.edit', [$song]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Songs/Edit')
                ->has('song')
                ->has('categories')
                ->has('statuses')
                ->has('pitches')
            );
    }

    public function test_index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('songs.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Songs/Index')
                ->has('songs')
                ->has('statuses')
                ->has('defaultStatuses')
                ->has('categories')
            );
    }

    public function test_show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

        $this->get(the_tenant_route('songs.show', [$song]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Songs/Show')
                ->has('song')
                ->has('attachment_types')
                ->has('status_count')
                ->has('voice_parts_count')
            );
    }

    public function test_show_returns_the_learning_status_for_the_user(): void
    {
        $song = Song::factory()->create();
        $user = User::factory()
            ->has(Membership::factory()
                ->hasAttached(
                    $song,
                    ['status' => 'assessment-ready']
                ))
            ->create();
        $user->membership->roles()->attach([Role::where('name', 'User')->value('id')]);

        $this->actingAs($user);

        $this->get(the_tenant_route('songs.show', $song))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Songs/Show')
                ->where('song.my_learning.status_name', 'Assessment Ready')
            );
    }

    public function test_show_returns_the_learning_summary(): void
    {
        $song = Song::factory()->create();
        User::factory()
            ->count(3)
            ->has(Membership::factory()
                ->hasAttached(
                    $song,
                    ['status' => 'assessment-ready']
                ))
            ->create();

        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('songs.show', $song))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Songs/Show')
                ->where('status_count.assessment_ready', 3)
            );
    }

    #[DataProvider('songProvider')]
    public function test_store_redirects_to_show($getData): void
    {
        Notification::fake();
        $this->actingAs($this->createUserWithRole('Music Team'));

        $data = $getData();
        $response = $this->post(the_tenant_route('songs.store'), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('songs', [
            'title' => $data['title'],
            'pitch_blown' => $data['pitch_blown'],
            'status_id' => $data['status'],
        ]);

        $song = Song::firstWhere('title', $data['title']);
        $response->assertRedirect(the_tenant_route('songs.show', [$song]));
        Notification::assertNothingSent();
    }

    #[DataProvider('songProvider')]
    public function test_store_sends_notification($getData): void
    {
        Notification::fake();
        $this->actingAs($this->createUserWithRole('Music Team'));

        $data = $getData();
        $data['send_notification'] = true;
        $this->post(the_tenant_route('songs.store'), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('songs', ['title' => $data['title']]);

        Notification::assertSentTo(auth()->user(), SongUploaded::class);
    }

    #[DataProvider('songProvider')]
    public function test_update_redirects_to_show($getData): void
    {
        Notification::fake();
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

        $data = $getData();
        $response = $this->put(the_tenant_route('songs.update', [$song]), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('songs', [
            'title' => $data['title'],
            'pitch_blown' => $data['pitch_blown'],
            'status_id' => $data['status'],
        ]);
        $response->assertRedirect(the_tenant_route('songs.show', [$song]));
        Notification::assertNothingSent();
    }

    #[DataProvider('songProvider')]
    public function test_update_sends_notification($getData): void
    {
        Notification::fake();
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

        $data = $getData();
        $data['send_notification'] = true;
        $this->put(the_tenant_route('songs.update', [$song]), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('songs', ['title' => $data['title']]);
        Notification::assertSentTo(auth()->user(), SongUpdated::class);
    }

    public static function songProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    return [
                        'title' => $faker->sentence(6, true),
                        'pitch_blown' => $faker->numberBetween(0, count(Song::getAllPitches())),
                        'status' => SongStatus::where('title', 'Active')->value('id'),
                        'categories' => [SongCategory::where('title', 'General')->value('id')],
                    ];
                },
            ],
        ];
    }
}
