<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Placement;
use App\Models\Membership;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SingerPlacementController
 */
class SingerPlacementControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $singer = Membership::factory()->create();

        $this->get(the_tenant_route('singers.placements.create', [$singer]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Placements/Create')
                ->has('singer')
            );
    }

    public function test_edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $singer = Membership::factory()
            ->has(Placement::factory())
            ->create();

        $this->get(the_tenant_route('singers.placements.edit', [$singer, $singer->placement]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Placements/Edit')
                ->has('singer')
                ->has('placement')
            );
    }

    #[DataProvider('placementProvider')]
    public function test_store_redirects_to_singer($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $singer = Membership::factory()->create();

        $data = $getData();
        $this->withoutExceptionHandling();
        $response = $this->post(the_tenant_route('singers.placements.store', [$singer]), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('placements', $data);
        $response->assertRedirect(the_tenant_route('singers.show', $singer));
    }

    #[DataProvider('placementProvider')]
    public function test_update_redirects_to_singer($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $singer = Membership::factory()
            ->has(Placement::factory())
            ->create();

        $data = $getData();
        $response = $this->put(the_tenant_route('singers.placements.update', [$singer, $singer->placement]), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('placements', $data);
        $response->assertRedirect(the_tenant_route('singers.show', $singer));
    }

    public static function placementProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    return [
                        'experience' => $faker->sentence(),
                        'instruments' => $faker->sentence(),
                        'skill_pitch' => $faker->numberBetween(1, 5),
                        'skill_harmony' => $faker->numberBetween(1, 5),
                        'skill_performance' => $faker->numberBetween(1, 5),
                        'skill_sightreading' => $faker->numberBetween(1, 5),
                    ];
                },
            ],
        ];
    }
}
