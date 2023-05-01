<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Song;
use App\Models\SongAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
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
        $this->assertModelMissing($attachment);
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
            'attachment; filename='.$attachment->filepath,
            $response->headers->get('content-disposition'),
        );
    }

    /**
     * @test
     * @dataProvider attachmentProvider
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

    /** @test */
    public function update_renames_the_file(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        Storage::fake('public');

        $song = Song::factory()->create();
        $attachment = SongAttachment::factory()->create([
            'song_id' => $song->id,
        ]);

        $response = $this->from(the_tenant_route('songs.show', [$song]))
            ->put(the_tenant_route('songs.attachments.update', [$song, 'attachment' => $attachment]), ['filename' => 'new.mp3']);

        $response->assertRedirect(the_tenant_route('songs.show', [$song]));
        $this->assertDatabaseHas('song_attachments', [
            'id' => $attachment->id,
            'filepath' => 'new.mp3',
        ]);

        // Unlike documents (which use hashed filenames), for song attachments we need to rename the actual file.
        // @TODO: Pick a file storage approach for documents AND songs and bloody stick to it.
        Storage::disk('public')->assertMissing($attachment->getPath());
        Storage::disk('public')
            ->assertExists(
                Str::of($attachment->getPath())
                    ->replace($attachment->filepath, 'new.mp3')
            );
    }

    public function attachmentProvider(): array
    {
        return [
            [
                function () {
                    $this->setUpFaker();

                    return [
                        'attachment_uploads' => [UploadedFile::fake()->create('random.mp3')],
                        'type' => $this->faker->randomElement(['sheet-music', 'learning-tracks', 'full-mix-demo']),
                    ];
                },
            ],
        ];
    }
}
