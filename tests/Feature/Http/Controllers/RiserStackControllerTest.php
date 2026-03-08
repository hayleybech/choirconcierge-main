<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\RiserStack;
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

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('stacks.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('RiserStacks/Create')
                ->has('voiceParts')
            );
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
