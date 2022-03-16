<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\RiserStack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RiserStackController
 */
class RiserStackControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('stacks.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('RiserStacks/Create')
                ->has('voice_parts')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $stack = RiserStack::factory()->create();

        $this->delete(the_tenant_route('stacks.destroy', ['stack' => $stack]))
            ->assertRedirect(the_tenant_route('stacks.index'));

        $this->assertSoftDeleted($stack);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $stack = RiserStack::factory()->create();

        $this->get(the_tenant_route('stacks.edit', ['stack' => $stack]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('RiserStacks/Edit')
                ->has('stack')
                ->has('voice_parts')
            );
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('stacks.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('RiserStacks/Index')
                ->has('stacks')
            );
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
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

    /**
     * @test
     * @dataProvider stackProvider
     */
    public function store_redirects_to_show($getData): void
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

    /**
     * @test
     * @dataProvider stackProvider
     */
    public function update_redirects_to_show($getData): void
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

    public function stackProvider(): array
    {
        return [
            [
                function () {
                    $this->setUpFaker();

                    return [
                        'title' => $this->faker->sentence(),
                        'rows' => $this->faker->numberBetween(2, 5),
                        'columns' => $this->faker->numberBetween(1, 8),
                        'front_row_length' => $this->faker->numberBetween(1, 10),
                        'front_row_on_floor' => $this->faker->boolean(),
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
