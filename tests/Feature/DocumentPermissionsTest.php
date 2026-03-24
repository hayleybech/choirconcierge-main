<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Folder;
use App\Models\Role;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('tenant');
    $this->initializeTenancy();
    
    // Create a role with NO documents_* abilities
    $this->noDocRole = Role::create([
        'name' => 'NoDocRole',
        'abilities' => [],
    ]);

    // Create a user with this role
    $this->membership = Membership::factory()->create();
    $this->membership->roles()->attach($this->noDocRole);
    $this->user = $this->membership->user;
});

test('user can view document if they can view the folder, even without documents_view ability', function () {
    // Create a folder where this user IS a specific viewer
    $folder = Folder::factory()->create();
    $folder->viewer_users()->attach($this->user->id);
    
    $document = Document::factory()->for($folder)->create();
    Storage::disk('tenant')->put($document->getPath(), 'test content');

    $this->actingAs($this->user);

    $response = $this->get(the_tenant_route('folders.documents.show', [$folder, $document]));
    
    $response->assertOk();
});

test('user can update document if they can update the folder, even without documents_create ability', function () {
    // Create a folder where this user IS a specific editor
    $folder = Folder::factory()->create();
    $folder->editor_users()->attach($this->user->id);
    
    $document = Document::factory()->for($folder)->create();

    $this->actingAs($this->user);

    $response = $this->put(the_tenant_route('folders.documents.update', [$folder, $document]), [
        'title' => 'New Title',
    ]);
    
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $this->assertEquals('New Title', $document->fresh()->title);
});

test('user can delete document if they can update the folder, even without documents_delete ability', function () {
    // Create a folder where this user IS a specific editor
    $folder = Folder::factory()->create();
    $folder->editor_users()->attach($this->user->id);
    
    $document = Document::factory()->for($folder)->create();
    Storage::disk('tenant')->put($document->getPath(), 'test content');

    $this->actingAs($this->user);

    $response = $this->delete(the_tenant_route('folders.documents.destroy', [$folder, $document]));
    
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $this->assertModelMissing($document);
});

test('user can upload documents if they can update the folder, even without documents_create ability', function () {
    // Create a folder where this user IS a specific editor
    $folder = Folder::factory()->create();
    $folder->editor_users()->attach($this->user->id);

    $this->actingAs($this->user);

    $response = $this->post(the_tenant_route('folders.documents.store', [$folder]), [
        'document_uploads' => [UploadedFile::fake()->create('test.pdf', 100)],
    ]);
    
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $this->assertCount(1, $folder->fresh()->documents);
});
