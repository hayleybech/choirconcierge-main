<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\VoicePartController
 */
class VoicePartControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('voice-parts.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('VoiceParts/Create')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $voice_part = VoicePart::factory()->create();

        $this->delete(the_tenant_route('voice-parts.destroy', [$voice_part]))
            ->assertRedirect(the_tenant_route('voice-parts.index'));

        $this->assertSoftDeleted($voice_part);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $voice_part = VoicePart::factory()->create();

        $this->get(the_tenant_route('voice-parts.edit', [$voice_part]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('VoiceParts/Edit')
                ->has('voice_part')
            );
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('voice-parts.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('VoiceParts/Index')
                ->has('parts')
            );
    }

    /**
     * @test
     * @dataProvider voicePartProvider
     */
    public function store_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $data = $getData();
        $response = $this->post(the_tenant_route('voice-parts.store'), $data);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('voice-parts.index'));

        $this->assertDatabaseHas('voice_parts', $data);
    }

    /**
     * @test
     * @dataProvider voicePartProvider
     */
    public function update_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $voice_part = VoicePart::factory()->create();

        $data = $getData();
        $this->put(the_tenant_route('voice-parts.update', [$voice_part]), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('voice-parts.index'));

        $this->assertDatabaseHas('voice_parts', $data);
    }

    public function voicePartProvider(): array
    {
        return [
            [
                function () {
                    $this->setUpFaker();

                    return [
                        'title' => $this->faker->word(),
                        'colour' => $this->faker->hexColor(),
                    ];
                },
            ],
        ];
    }
}
