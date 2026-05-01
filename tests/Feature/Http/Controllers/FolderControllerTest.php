<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Enums\SingerStatus;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FolderController
 */
class FolderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_can_sort_folders_by_name(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        Folder::factory()->create(['title' => 'C Folder']);
        Folder::factory()->create(['title' => 'A Folder']);
        Folder::factory()->create(['title' => 'B Folder']);

        $this->get(the_tenant_route('folders.index', ['sort' => 'title']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Folders/Index')
                ->has('folders', 3)
                ->where('folders.0.title', 'A Folder')
                ->where('folders.1.title', 'B Folder')
                ->where('folders.2.title', 'C Folder')
            );
    }

    public function test_index_can_sort_folders_by_date(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $folder1 = Folder::factory()->create(['title' => 'Old Folder', 'created_at' => now()->subDay()]);
        $folder2 = Folder::factory()->create(['title' => 'New Folder', 'created_at' => now()]);

        $this->get(the_tenant_route('folders.index', ['sort' => '-created_at']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Folders/Index')
                ->has('folders', 2)
                ->where('folders.0.title', 'New Folder')
                ->where('folders.1.title', 'Old Folder')
            );
    }

    public function test_index_can_filter_by_name(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $folderMatch = Folder::factory()->create(['title' => 'Important Meeting']);
        $folderNoMatch = Folder::factory()->create(['title' => 'Other Stuff']);

        $docMatch = Document::factory()
            ->create([
                'document_upload' => \Illuminate\Http\UploadedFile::fake()->create('Meeting Minutes.pdf', 5),
                'folder_id' => $folderNoMatch->id,
            ]);

        $this->get(the_tenant_route('folders.index', ['filter' => ['title' => 'Meeting']]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Folders/Index')
                ->has('folders', 1)
                ->where('folders.0.title', 'Important Meeting')
                ->has('documents', 1)
                ->where('documents.0.title', 'Meeting Minutes.pdf')
            );
    }

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('folders.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Folders/Create')
                ->has('singerStatuses')
            );
    }

    public function test_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $folder = Folder::factory()->create();

        $this->delete(the_tenant_route('folders.destroy', [$folder]))
            ->assertRedirect(the_tenant_route('folders.index'));

        $this->assertSoftDeleted($folder);
    }

    public function test_index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('folders.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Folders/Index')
                ->has('folders')
            );
    }

    #[DataProvider('folderProvider')]
    public function test_store_redirects_to_index($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $data = $getData();
        $this->post(the_tenant_route('folders.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('folders.index'));

        $this->assertDatabaseHas('folders', $data);
    }

    public function test_can_save_and_display_singer_statuses_for_folders(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $statusValue = SingerStatus::MEMBERS->value;

        // 1. POST request to create a folder with singer statuses
        $this->post(the_tenant_route('folders.store'), [
            'title' => 'Test Folder',
            'viewer_singer_statuses' => [$statusValue],
            'editor_singer_statuses' => [$statusValue],
        ])
            ->assertRedirect(the_tenant_route('folders.index'));

        $folder = Folder::where('title', 'Test Folder')->first();
        $this->assertNotNull($folder);

        // Verify it's saved in the database
        $this->assertDatabaseHas('folder_viewers', [
            'folder_id' => $folder->id,
            'viewer_id' => $statusValue,
            'viewer_type' => SingerStatus::class,
        ]);
        $this->assertDatabaseHas('folder_editors', [
            'folder_id' => $folder->id,
            'editor_id' => $statusValue,
            'editor_type' => SingerStatus::class,
        ]);

        // 2. GET request to edit the folder and verify Inertia data
        $this->get(the_tenant_route('folders.edit', $folder))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('folder.viewer_singer_statuses.0.viewer_id', $statusValue)
                ->where('folder.editor_singer_statuses.0.editor_id', $statusValue)
            );
    }

    #[DataProvider('folderProvider')]
    public function test_update_redirects_to_index($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $folder = Folder::factory()->create();

        $data = $getData();
        $this->put(the_tenant_route('folders.update', [$folder]), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('folders.index'));

        $this->assertDatabaseHas('folders', $data);
    }

    public static function folderProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    return [
                        'title' => $faker->sentence(),
                    ];
                },
            ],
        ];
    }
}
