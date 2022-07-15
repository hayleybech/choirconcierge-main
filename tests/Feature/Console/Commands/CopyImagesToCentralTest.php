<?php

use App\Models\Singer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

test('it updates the disk name in the DB', function () {
    Storage::fake('public');

    $singer = Singer::factory()->create();
    $singer->user->addMedia(UploadedFile::fake()->image('test.jpg'))->toMediaCollection('avatar');

    assertDatabaseHas('media', [
        'id' => 1,
        'file_name' => 'test.jpg',
        'disk' => 'public',
        'conversions_disk' => 'public',
    ]);

    artisan('once-off:copy-images')->assertSuccessful();

    assertDatabaseHas('media', [
        'id' => 1,
        'disk' => 'global-public',
        'conversions_disk' => 'global-public',
    ]);
});

test('it copies the file to central storage', function() {
    Storage::fake('public');

    $singer = Singer::factory()->create();
    $singer->user->addMedia(UploadedFile::fake()->image('test.jpg'))->toMediaCollection('avatar');

    Storage::disk('public')->assertExists('1/test.jpg');

    artisan('once-off:copy-images')->assertSuccessful();

    Storage::disk('global-public')->assertExists('1/test.jpg');
});
