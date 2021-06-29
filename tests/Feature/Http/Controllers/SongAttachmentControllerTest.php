<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Song;
use App\Models\SongAttachment;
use App\Models\SongAttachmentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SongAttachmentController
 */
class SongAttachmentControllerTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/**
	 * @test
	 */
	public function destroy_redirects_to_song(): void
	{
		$this->actingAs($this->createUserWithRole('Music Team'));

		Storage::fake('public');

		$song = Song::factory()->create();
		$attachment = SongAttachment::factory()->create([
			'song_id' => $song->id,
		]);

		$response = $this->delete(the_tenant_route('songs.attachments.destroy', [$song, 'attachment' => $attachment]));

		$response->assertRedirect(the_tenant_route('songs.show', [$song]));
		$this->assertDeleted($attachment);
		Storage::disk('public')->assertMissing($attachment->getPath());
	}

	/**
	 * @test
	 */
	public function show_returns_file(): void
	{
		$this->actingAs($this->createUserWithRole('Music Team'));

		Storage::fake('public');

		$song = Song::factory()->create();
		$attachment = SongAttachment::factory()->create([
			'song_id' => $song->id,
		]);

		$response = $this->get(the_tenant_route('songs.attachments.show', [$song, 'attachment' => $attachment]));

		$response->assertOk();
		self::assertEquals(
			'attachment; filename=' . $attachment->filepath,
			$response->headers->get('content-disposition'),
		);
	}

	/**
	 * @test
	 * @dataProvider eventProvider
	 */
	public function store_redirects_to_song($getData): void
	{
		$this->actingAs($this->createUserWithRole('Music Team'));

		Storage::fake('public');
		$song = Song::factory()->create();

		$data = $getData();
		$response = $this->post(the_tenant_route('songs.attachments.store', [$song]), $data);

		$response->assertSessionHasNoErrors();
		$this->assertDatabaseHas('song_attachments', [
			'filepath' => $data['attachment_uploads'][0]->name,
		]);

		$attachment = SongAttachment::firstWhere('filepath', $data['attachment_uploads'][0]->name);
		Storage::disk('public')->assertExists($attachment->getPath());
		$response->assertRedirect(the_tenant_route('songs.show', [$song]));
	}

	public function eventProvider(): array
	{
		return [
			[
				function () {
					$this->setUpFaker();
					return [
						'attachment_uploads' => [UploadedFile::fake()->create('random.mp3')],
						'category' => SongAttachmentCategory::inRandomOrder()->value('id'),
					];
				},
			],
		];
	}
}
