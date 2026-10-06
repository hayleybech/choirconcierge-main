<?php

use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('document extension cannot be changed when renamed', function () {
    Storage::fake('tenant');
    $this->actingAs($this->createUserWithRole('Music Team'));
    $folder = Folder::factory()->hasDocuments()->create();
    $document = $folder->documents->first();

    $this->from(the_tenant_route('folders.index'))
        ->put(the_tenant_route('folders.documents.update', [$folder, $document]), [
            'title' => 'renamed.pdf',
        ])
        ->assertSessionHasErrors('title')
        ->assertRedirect(the_tenant_route('folders.index'));

    expect($document->fresh()->title)->not->toBe('renamed.pdf');
});
