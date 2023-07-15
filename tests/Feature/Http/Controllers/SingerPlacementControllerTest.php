<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Placement;
use App\Models\Membership;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SingerPlacementController
 */
class SingerPlacementControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $singer = Membership::factory()->create();

        $this->get(the_tenant_route('singers.placements.create', [$singer]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Placements/Create')
                ->has('singer')
                ->has('voice_parts')
            );
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
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
                ->has('voice_parts')
            );
    }

    /**
     * @test
     * @dataProvider placementProvider
     */
    public function store_redirects_to_singer($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $singer = Membership::factory()->create();

        $data = $getData();
        $this->withoutExceptionHandling();
        $response = $this->post(the_tenant_route('singers.placements.store', [$singer]), $data)
            ->assertSessionHasNoErrors();

        unset($data['voice_part_id']);
        $this->assertDatabaseHas('placements', $data);
        $response->assertRedirect(the_tenant_route('singers.show', $singer));
    }

    /**
     * @test
     * @dataProvider placementProvider
     */
    public function update_redirects_to_singer($getData): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $singer = Membership::factory()
            ->has(Placement::factory())
            ->create();

        $data = $getData();
        $response = $this->put(the_tenant_route('singers.placements.update', [$singer, $singer->placement]), $data)
            ->assertSessionHasNoErrors();

        unset($data['voice_part_id']);
        $this->assertDatabaseHas('placements', $data);
        $response->assertRedirect(the_tenant_route('singers.show', $singer));
    }

    public function placementProvider(): array
    {
        return [
            [
                function () {
                    $this->setUpFaker();

                    return [
                        'experience' => $this->faker->sentence(),
                        'instruments' => $this->faker->sentence(),
                        'skill_pitch' => $this->faker->numberBetween(1, 5),
                        'skill_harmony' => $this->faker->numberBetween(1, 5),
                        'skill_performance' => $this->faker->numberBetween(1, 5),
                        'skill_sightreading' => $this->faker->numberBetween(1, 5),

                        'voice_part_id' => VoicePart::inRandomOrder()->value('id'),
                    ];
                },
            ],
        ];
    }
}
