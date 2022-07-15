<?php

use App\Models\Singer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

test('it removes the file from tenant storage', function() {
    Storage::fake('public');

    $singer = Singer::factory()->create();
    $singer->user->addMedia(UploadedFile::fake()->image('test.jpg'))->toMediaCollection('avatar');

    Storage::disk('public')->assertExists('1/test.jpg');

    artisan('once-off:delete-images')->assertSuccessful();

    Storage::disk('public')->assertMissing('1/test.jpg');
});
