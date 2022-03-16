<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FolderController
 */
class FolderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('folders.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Folders/Create')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $folder = Folder::factory()->create();

        $this->delete(the_tenant_route('folders.destroy', [$folder]))
            ->assertRedirect(the_tenant_route('folders.index'));

        $this->assertSoftDeleted($folder);
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('folders.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Folders/Index')
                ->has('folders')
            );
    }

    /**
     * @test
     * @dataProvider folderProvider
     */
    public function store_redirects_to_index($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $data = $getData();
        $this->post(the_tenant_route('folders.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('folders.index'));

        $this->assertDatabaseHas('folders', $data);
    }

    /**
     * @test
     * @dataProvider folderProvider
     */
    public function update_redirects_to_index($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $folder = Folder::factory()->create();

        $data = $getData();
        $this->put(the_tenant_route('folders.update', [$folder]), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('folders.index'));

        $this->assertDatabaseHas('folders', $data);
    }

    public function folderProvider(): array
    {
        return [
            [
                function () {
                    $this->setUpFaker();

                    return [
                        'title' => $this->faker->sentence(),
                    ];
                },
            ],
        ];
    }
}
