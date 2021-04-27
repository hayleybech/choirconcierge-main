<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $response = $this->get(the_tenant_route('folders.create'));

        $response->assertOk();
        $response->assertViewIs('folders.create');
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $folder = Folder::factory()->create();

        $response = $this->delete(the_tenant_route('folders.destroy', [$folder]));

	    $this->assertSoftDeleted($folder);
	    $response->assertRedirect(the_tenant_route('folders.index'));
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $response = $this->get(the_tenant_route('folders.index'));

        $response->assertOk();
        $response->assertViewIs('folders.index');
        $response->assertViewHas('folders');
    }

    /**
     * @test
     * @dataProvider folderProvider
     */
    public function store_redirects_to_index($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

	    $data = $getData();
        $response = $this->post(the_tenant_route('folders.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('folders', $data);
        $response->assertRedirect(the_tenant_route('folders.index'));
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
        $response = $this->put(the_tenant_route('folders.update', [$folder]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('folders', $data);
        $response->assertRedirect(the_tenant_route('folders.index'));
    }

	public function folderProvider(): array
	{
		return [
			[
				function() {
					$this->setUpFaker();
					return [
						'title'     => $this->faker->sentence,
					];
				}
			]
		];
	}
}
