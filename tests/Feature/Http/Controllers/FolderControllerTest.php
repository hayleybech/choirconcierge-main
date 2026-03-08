<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Folder;
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

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('folders.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Folders/Create')
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
