<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RoleController
 */
class RoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('roles.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Roles/Create')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->withoutExceptionHandling();
        $role = Role::factory()->create();

        $this->delete(the_tenant_route('roles.destroy', [$role]))
            ->assertRedirect(the_tenant_route('roles.index'));

        $this->assertSoftDeleted($role);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $role = Role::factory()->create();

        $this->get(the_tenant_route('roles.edit', [$role]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Roles/Edit')
                ->has('role')
            );
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('roles.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Roles/Index')
                ->has('roles')
            );
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $role = Role::factory()->create();

        $this->get(the_tenant_route('roles.show', [$role]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Roles/Show')
                ->has('role')
            );
    }

    /**
     * @test
     * @dataProvider roleProvider
     */
    public function store_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $data = $getData();
        $response = $this->post(the_tenant_route('roles.store'), $data)
            ->assertSessionHasNoErrors();

        $role = Role::firstWhere('name', $data['name']);
        $response->assertRedirect(the_tenant_route('roles.show', $role));
    }

    /**
     * @test
     * @dataProvider roleProvider
     */
    public function update_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $role = Role::factory()->create();

        $data = $getData();
        $response = $this->put(the_tenant_route('roles.update', [$role]), $data)
            ->assertSessionHasNoErrors();

        // assertDatabaseHas didn't work for the json abilities
        $updated_role = Role::find($role->id);
        self::assertEquals($data['name'], $updated_role->name);
        self::assertEquals($data['abilities'], $updated_role->abilities);

        $response->assertRedirect(the_tenant_route('roles.show', $role));
    }

    public function roleProvider(): array
    {
        return [
            [
                function () {
                    $this->setUpFaker();

                    return [
                        'name' => $this->faker->sentence(3),
                        'abilities' => $this->faker->randomElements(Role::ALL_ABILITIES, 20),
                    ];
                },
            ],
        ];
    }
}
