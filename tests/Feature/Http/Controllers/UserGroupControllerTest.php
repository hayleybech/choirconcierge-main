<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UserGroupController
 */
class UserGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('groups.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('MailingLists/Create')
                ->has('roles')
                ->has('voiceParts')
                ->has('singerCategories')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $this->delete(the_tenant_route('groups.destroy', ['group' => $group]))
            ->assertRedirect(the_tenant_route('groups.index'));

        $this->assertSoftDeleted($group);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $response = $this->get(the_tenant_route('groups.edit', ['group' => $group]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('MailingLists/Edit')
                ->has('list')
                ->has('roles')
                ->has('voiceParts')
                ->has('singerCategories')
            );
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $response = $this->get(the_tenant_route('groups.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('MailingLists/Index')
                ->has('lists')
            );
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $this->get(the_tenant_route('groups.show', ['group' => $group]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('MailingLists/Show')
                ->has('list')
            );
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function store_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $data = $getData();
        $response = $this->post(the_tenant_route('groups.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('user_groups', $data);

        $group = UserGroup::firstWhere('title', $data['title']);
        $response->assertRedirect(the_tenant_route('groups.show', [$group]));
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function update_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $data = $getData();
        $response = $this->put(the_tenant_route('groups.update', ['group' => $group]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('user_groups', $data);
        $response->assertRedirect(the_tenant_route('groups.show', [$group]));
    }

    public function eventProvider(): array
    {
        return [
            [
                function () {
                    $this->setUpFaker();

                    return [
                        'title' => $this->faker->sentence(),
                        'slug' => $this->faker->unique()->slug(),
                        'list_type' => $this->faker->randomElement(['public', 'chat', 'distribution']),
                    ];
                },
            ],
        ];
    }
}
