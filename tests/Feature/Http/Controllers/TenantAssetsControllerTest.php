<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Song;
use App\Models\SongAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\from;
use function Pest\Laravel\get;
use function PHPUnit\Framework\assertEquals;

/**
 * @see \App\Http\Controllers\TenantAssetsController
 */
uses(RefreshDatabase::class, WithFaker::class);

it('returns a file', function() {
    actingAs($this->createUserWithRole('Music Team'));

    $song = Song::factory()->create();
    $attachment = SongAttachment::factory()->create([
        'song_id' => $song->id,
    ]);

    $response = from(the_tenant_route('dash'))->get($attachment->download_url);

    $response->assertOk();

    assertEquals(
        $attachment->filepath,
        $response->getFile()->getFileName(),
    );
});
