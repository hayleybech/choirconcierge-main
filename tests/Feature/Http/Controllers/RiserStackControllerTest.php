<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Ensemble;
use App\Models\Membership;
use App\Models\RiserStack;
use App\Models\Role;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RiserStackController
 */
class RiserStackControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function createUserWithRole(string $roleName): \App\Models\User
    {
        $role = Role::firstOrCreate(['name' => $roleName], [
            'abilities' => $roleName === 'Music Team' 
                ? ['riser_stacks_view', 'riser_stacks_create', 'riser_stacks_update', 'riser_stacks_delete']
                : ['riser_stacks_view']
        ]);

        $singer = Membership::factory()->create();
        $singer->roles()->attach([$role->id]);

        return $singer->user;
    }

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('stacks.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('RiserStacks/Create')
                ->has('voiceParts')
                ->has('ensembles')
            );
    }

    public function test_index_filters_by_ensemble(): void
    {
        $user = $this->createUserWithRole('Music Team');
        $this->actingAs($user);

        $ensemble1 = Ensemble::factory()->create();
        $ensemble2 = Ensemble::factory()->create();

        $stack1 = RiserStack::factory()->create();
        $stack1->ensembles()->attach($ensemble1);

        $stack2 = RiserStack::factory()->create();
        $stack2->ensembles()->attach($ensemble2);

        $this->get(the_tenant_route('stacks.index', ['filter' => ['ensembles.id' => [$ensemble1->id]]]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('stacks.data', 1)
                ->where('stacks.data.0.id', $stack1->id)
            );
    }

    public function test_index_hides_stacks_outside_user_ensembles_for_non_managers(): void
    {
        $user = $this->createUserWithRole('Members'); 
        $this->actingAs($user);

        $ensemble1 = Ensemble::factory()->create();
        $ensemble2 = Ensemble::factory()->create();

        // Enrol user in ensemble 1
        $user->membership->enrolments()->create(['ensemble_id' => $ensemble1->id]);

        $stack1 = RiserStack::factory()->create();
        $stack1->ensembles()->attach($ensemble1);

        $stack2 = RiserStack::factory()->create();
        $stack2->ensembles()->attach($ensemble2);

        $this->get(the_tenant_route('stacks.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('stacks.data', 1)
                ->where('stacks.data.0.id', $stack1->id)
            );
    }

    public function test_show_restricts_access_to_stacks_outside_user_ensembles(): void
    {
        $user = $this->createUserWithRole('Members');
        $this->actingAs($user);

        $ensemble1 = Ensemble::factory()->create();
        $ensemble2 = Ensemble::factory()->create();

        // Enrol user in ensemble 1
        $user->membership->enrolments()->create(['ensemble_id' => $ensemble1->id]);

        $stack1 = RiserStack::factory()->create();
        $stack1->ensembles()->attach($ensemble1);

        $stack2 = RiserStack::factory()->create();
        $stack2->ensembles()->attach($ensemble2);

        // Can view stack 1
        $this->get(the_tenant_route('stacks.show', $stack1))->assertOk();

        // Cannot view stack 2
        $this->get(the_tenant_route('stacks.show', $stack2))->assertForbidden();
    }

    public function test_store_syncs_ensembles(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $ensemble = Ensemble::factory()->create();
        $faker = Factory::create();

        $data = [
            'title' => $faker->sentence(),
            'rows' => 4,
            'columns' => 4,
            'front_row_length' => 1,
            'front_row_on_floor' => false,
            'singer_positions' => [
                ['id' => 1, 'position' => ['row' => 1, 'column' => 1]]
            ],
            'ensembles' => [$ensemble->id],
        ];

        $this->post(the_tenant_route('stacks.store'), $data)
            ->assertSessionHasNoErrors();

        $stack = RiserStack::where('title', $data['title'])->first();
        $this->assertTrue($stack->ensembles->contains($ensemble));
    }

    public function test_update_syncs_ensembles(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $stack = RiserStack::factory()->create();
        $ensemble = Ensemble::factory()->create();
        $faker = Factory::create();

        $data = [
            'title' => $faker->sentence(),
            'rows' => 4,
            'columns' => 4,
            'front_row_length' => 1,
            'front_row_on_floor' => false,
            'singer_positions' => [
                ['id' => 1, 'position' => ['row' => 1, 'column' => 1]]
            ],
            'ensembles' => [$ensemble->id],
        ];

        $this->put(the_tenant_route('stacks.update', $stack), $data)
            ->assertSessionHasNoErrors();

        $this->assertTrue($stack->fresh()->ensembles->contains($ensemble));
    }

    public function test_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $stack = RiserStack::factory()->create();

        $this->delete(the_tenant_route('stacks.destroy', ['stack' => $stack]))
            ->assertRedirect(the_tenant_route('stacks.index'));

        $this->assertSoftDeleted($stack);
    }

    public function test_edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $stack = RiserStack::factory()->create();

        $this->get(the_tenant_route('stacks.edit', ['stack' => $stack]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('RiserStacks/Edit')
                ->has('stack')
                ->has('voiceParts')
            );
    }

    public function test_index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('stacks.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('RiserStacks/Index')
                ->has('stacks')
            );
    }

    public function test_show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $stack = RiserStack::factory()->create();

        $this->get(the_tenant_route('stacks.show', ['stack' => $stack]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('RiserStacks/Show')
                ->has('stack')
            );
    }

    public function test_bulk_update_syncs_ensembles_to_multiple_stacks(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $stacks = RiserStack::factory()->count(3)->create();
        $ensembles = Ensemble::factory()->count(2)->create();

        $data = [
            'stack_ids' => $stacks->pluck('id')->toArray(),
            'ensemble_ids' => $ensembles->pluck('id')->toArray(),
        ];

        $this->post(the_tenant_route('stacks.bulk-update'), $data)
            ->assertRedirect(the_tenant_route('stacks.index'))
            ->assertSessionHasNoErrors();

        foreach ($stacks as $stack) {
            $this->assertEquals(2, $stack->fresh()->ensembles()->count());
            $this->assertTrue($stack->fresh()->ensembles->contains($ensembles[0]));
            $this->assertTrue($stack->fresh()->ensembles->contains($ensembles[1]));
        }
    }

    #[DataProvider('stackProvider')]
    public function test_store_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $data = $getData();

        $post = $data;
        if ($post['front_row_on_floor'] === false) {
            unset($post['front_row_on_floor']);
        }
        $response = $this->post(the_tenant_route('stacks.store'), $post);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('riser_stacks', [
            'title' => $data['title'],
            'rows' => $data['rows'],
            'columns' => $data['columns'],
            'front_row_length' => $data['front_row_length'],
            'front_row_on_floor' => (int) $data['front_row_on_floor'],
        ]);

        $stack = RiserStack::firstWhere('title', $data['title']);
        $response->assertRedirect(the_tenant_route('stacks.show', [$stack]));
    }

    #[DataProvider('stackProvider')]
    public function test_update_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $stack = RiserStack::factory()->create();

        $data = $getData();

        $post = $data;
        if ($post['front_row_on_floor'] === false) {
            unset($post['front_row_on_floor']);
        }
        $response = $this->put(the_tenant_route('stacks.update', ['stack' => $stack]), $post);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('riser_stacks', [
            'title' => $data['title'],
            'rows' => $data['rows'],
            'columns' => $data['columns'],
            'front_row_length' => $data['front_row_length'],
            'front_row_on_floor' => (int) $data['front_row_on_floor'],
        ]);
        $response->assertRedirect(the_tenant_route('stacks.show', [$stack]));
    }

    public static function stackProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    return [
                        'title' => $faker->sentence(),
                        'rows' => $faker->numberBetween(2, 5),
                        'columns' => $faker->numberBetween(1, 8),
                        'front_row_length' => $faker->numberBetween(1, 10),
                        'front_row_on_floor' => $faker->boolean(),
                        'singer_positions' => [
                            [
                                'id' => 0,
                                'position' => [
                                    'row' => 1,
                                    'column' => 1,
                                ],
                            ],
                        ],
                    ];
                },
            ],
        ];
    }
}
