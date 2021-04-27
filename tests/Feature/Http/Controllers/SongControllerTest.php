<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Song;
use App\Models\SongCategory;
use App\Models\SongStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SongController
 */
class SongControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $response = $this->get(the_tenant_route('songs.create'));

        $response->assertOk();
        $response->assertViewIs('songs.create');
        $response->assertViewHas('categories');
        $response->assertViewHas('statuses');
        $response->assertViewHas('pitches');
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

        $response = $this->delete(the_tenant_route('songs.destroy', [$song]));

	    $this->assertSoftDeleted($song);
	    $response->assertRedirect(the_tenant_route('songs.index'));
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

        $this->withoutExceptionHandling();
        $response = $this->get(the_tenant_route('songs.edit', [$song]));

        $response->assertOk();
        $response->assertViewIs('songs.edit');
        $response->assertViewHas('song');
        $response->assertViewHas('categories');
        $response->assertViewHas('statuses');
        $response->assertViewHas('pitches');
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $response = $this->get(the_tenant_route('songs.index'));

        $response->assertOk();
        $response->assertViewIs('songs.index');
        $response->assertViewHas('all_songs');
        $response->assertViewHas('active_songs');
        $response->assertViewHas('learning_songs');
        $response->assertViewHas('pending_songs');
        $response->assertViewHas('archived_songs');
        $response->assertViewHas('filters');
        $response->assertViewHas('sorts');
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

        $response = $this->get(the_tenant_route('songs.show', [$song]));

        $response->assertOk();
        $response->assertViewIs('songs.show');
        $response->assertViewHas('song');
        $response->assertViewHas('categories_keyed');
    }

    /**
     * @test
     * @dataProvider songProvider
     */
    public function store_redirects_to_show($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

	    $data = $getData();
        $response = $this->post(the_tenant_route('songs.store'), $data);

	    $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('songs', [
        	'title'         => $data['title'],
	        'pitch_blown'   => $data['pitch_blown'],
	        'status_id'     => $data['status'],
        ]);

        $song = Song::firstWhere('title', $data['title']);
        $response->assertRedirect(the_tenant_route('songs.show', [$song]));
    }

    /**
     * @test
     * @dataProvider songProvider
     */
    public function update_redirects_to_show($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();

	    $data = $getData();
        $response = $this->put(the_tenant_route('songs.update', [$song]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('songs', [
	        'title'         => $data['title'],
	        'pitch_blown'   => $data['pitch_blown'],
	        'status_id'     => $data['status'],
        ]);
        $response->assertRedirect(the_tenant_route('songs.show', [$song]));
    }

	public function songProvider(): array
	{
		return [
			[
				function() {
					$this->setUpFaker();
					return [
						'title'         => $this->faker->sentence(6, true),
						'pitch_blown'   => $this->faker->numberBetween(0, count(Song::getAllPitches())),
						'status'     => SongStatus::where('title', 'Active')->value('id'),
						'categories'    => [SongCategory::where('title', 'General')->value('id')]
					];
				}
			]
		];
	}
}
