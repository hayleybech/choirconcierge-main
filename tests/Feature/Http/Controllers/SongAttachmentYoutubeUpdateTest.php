<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Song;
use App\Models\SongAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongAttachmentYoutubeUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_youtube_attachment_title_and_url(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();
        $attachment = SongAttachment::factory()->create([
            'song_id' => $song->id,
            'type' => 'youtube',
            'title' => 'Old Title',
            'filepath' => 'https://www.youtube.com/watch?v=old',
        ]);

        $response = $this->put(the_tenant_route('songs.attachments.update', [$song, 'attachment' => $attachment]), [
            'type' => 'youtube',
            'title' => 'New Title',
            'url' => 'https://www.youtube.com/watch?v=new',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('song_attachments', [
            'id' => $attachment->id,
            'title' => 'New Title',
            'filepath' => 'https://www.youtube.com/watch?v=new',
        ]);
    }

    public function test_updating_non_youtube_attachment_still_works_with_filename(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $song = Song::factory()->create();
        $attachment = SongAttachment::factory()->create([
            'song_id' => $song->id,
            'type' => 'sheet-music',
            'filepath' => 'old.pdf',
        ]);

        // Mock storage because update method tries to move files
        \Storage::fake('tenant');
        \Storage::disk('tenant')->put("songs/{$song->id}/old.pdf", 'content');

        $response = $this->put(the_tenant_route('songs.attachments.update', [$song, 'attachment' => $attachment]), [
            'filename' => 'new.pdf',
        ]);

        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('song_attachments', [
            'id' => $attachment->id,
            'filepath' => 'new.pdf',
        ]);
        
        \Storage::disk('tenant')->assertExists("songs/{$song->id}/new.pdf");
        \Storage::disk('tenant')->assertMissing("songs/{$song->id}/old.pdf");
    }
}
