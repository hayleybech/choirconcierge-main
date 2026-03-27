<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\UserGroup;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UserGroupController
 */
class UserGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('groups.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MailingLists/Create')
                ->has('roles')
                ->has('voiceParts')
                ->has('singerCategories')
            );
    }

    public function test_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $this->delete(the_tenant_route('groups.destroy', ['group' => $group]))
            ->assertRedirect(the_tenant_route('groups.index'));

        $this->assertSoftDeleted($group);
    }

    public function test_bulk_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $groups = UserGroup::factory()->count(3)->create();

        $this->post(the_tenant_route('groups.bulk-destroy'), [
            'group_ids' => $groups->pluck('id')->toArray(),
        ])->assertRedirect(the_tenant_route('groups.index'));

        foreach ($groups as $group) {
            $this->assertSoftDeleted($group);
        }
    }

    public function test_edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $response = $this->get(the_tenant_route('groups.edit', ['group' => $group]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MailingLists/Edit')
                ->has('list')
                ->has('roles')
                ->has('voiceParts')
                ->has('singerCategories')
            );
    }

    public function test_index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $response = $this->get(the_tenant_route('groups.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MailingLists/Index')
                ->has('lists')
            );
    }

    public function test_show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $this->get(the_tenant_route('groups.show', ['group' => $group]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MailingLists/Show')
                ->has('list')
            );
    }

    #[DataProvider('eventProvider')]
    public function test_store_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $data = $getData();
        $response = $this->post(the_tenant_route('groups.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('user_groups', $data);

        $group = UserGroup::firstWhere('title', $data['title']);
        $response->assertRedirect(the_tenant_route('groups.show', [$group]));
    }

    #[DataProvider('eventProvider')]
    public function test_update_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $data = $getData();
        $response = $this->put(the_tenant_route('groups.update', ['group' => $group]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('user_groups', $data);
        $response->assertRedirect(the_tenant_route('groups.show', [$group]));
    }

    public static function eventProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    return [
                        'title' => $faker->sentence(),
                        'slug' => $faker->unique()->slug(),
                        'list_type' => $faker->randomElement(['public', 'chat', 'distribution']),
                    ];
                },
            ],
        ];
    }
}
