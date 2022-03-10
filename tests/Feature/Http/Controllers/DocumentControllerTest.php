<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DocumentController
 */
class DocumentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function destroy_redirects_back(): void
    {
        Storage::fake('public');

        $this->actingAs($this->createUserWithRole('Music Team'));

        $folder = Folder::factory()
            ->hasDocuments()
            ->create();

        $response = $this->from(the_tenant_route('folders.index'))->delete(
            the_tenant_route('folders.documents.destroy', [$folder, $folder->documents->first()]),
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('folders.index'));
        $this->assertDeleted($folder->documents->first());
    }

    /**
     * @test
     */
    public function show_returns_file(): void
    {
        Storage::fake('public');

        $this->actingAs($this->createUserWithRole('Music Team'));

        $folder = Folder::factory()
            ->hasDocuments()
            ->create();
        Storage::disk('public')->assertExists($folder->documents->first()->getPath());

        $response = $this->get(the_tenant_route('folders.documents.show', [$folder, $folder->documents->first()]));

        $response->assertOk();
        self::assertEquals(
            'attachment; filename='.$folder->documents->first()->filepath,
            $response->headers->get('content-disposition'),
        );
    }

    /**
     * @test
     */
    public function store_redirects_back(): void
    {
        Storage::fake('public');

        $this->actingAs($this->createUserWithRole('Music Team'));

        $folder = Folder::factory()->create();

        $filename = $this->faker->word().'.'.$this->faker->fileExtension();
        $data = [
            'document_uploads' => [UploadedFile::fake()->create($filename, 5)],
        ];
        $response = $this->from(the_tenant_route('folders.index'))->post(
            the_tenant_route('folders.documents.store', [$folder]),
            $data,
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('folders.index'));
        $this->assertDatabaseHas('documents', [
            'folder_id' => $folder->id,
            'filepath' => $data['document_uploads'][0]->hashName(),
        ]);
        $document = Document::firstWhere('filepath', $data['document_uploads'][0]->hashName());
        Storage::disk('public')->assertExists($document->getPath());
    }
}
