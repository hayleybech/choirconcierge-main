<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Song;
use App\Models\SongAttachment;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Storage;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SongAttachmentController
 */
class SongAttachmentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_destroy_redirects_to_song(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        Storage::fake('tenant');

        $song = Song::factory()->create();
        $attachment = SongAttachment::factory()->create([
            'song_id' => $song->id,
        ]);

        $response = $this->delete(the_tenant_route('songs.attachments.destroy', [$song, 'attachment' => $attachment]));

        $response->assertRedirect(the_tenant_route('songs.show', [$song]));
        $this->assertModelMissing($attachment);
        Storage::disk('tenant')->assertMissing($attachment->getPath());
    }

    public function test_show_returns_file(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        Storage::fake('tenant');

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

    #[DataProvider('attachmentProvider')]
    public function test_store_redirects_to_song($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        Storage::fake('tenant');
        $song = Song::factory()->create();

        $data = $getData();
        $response = $this->post(the_tenant_route('songs.attachments.store', [$song]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('song_attachments', [
            'filepath' => $data['attachment_uploads'][0]->name,
        ]);

        $attachment = SongAttachment::firstWhere('filepath', $data['attachment_uploads'][0]->name);
        Storage::disk('tenant')->assertExists($attachment->getPath());
        $response->assertRedirect(the_tenant_route('songs.show', [$song]));
    }

    public function test_update_renames_the_file(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        Storage::fake('tenant');

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
        Storage::disk('tenant')->assertMissing($attachment->getPath());
        Storage::disk('tenant')
            ->assertExists(
                Str::of($attachment->getPath())
                    ->replace($attachment->filepath, 'new.mp3')
            );
    }

    public static function attachmentProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    return [
                        'attachment_uploads' => [UploadedFile::fake()->create('random.mp3')],
                        'type' => $faker->randomElement(['sheet-music', 'learning-tracks', 'full-mix-demo']),
                    ];
                },
            ],
        ];
    }
}
