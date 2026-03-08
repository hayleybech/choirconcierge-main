<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\VoicePart;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\VoicePartController
 */
class VoicePartControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('voice-parts.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('VoiceParts/Create')
            );
    }

    public function test_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $voice_part = VoicePart::factory()->create();

        $this->delete(the_tenant_route('voice-parts.destroy', [$voice_part]))
            ->assertRedirect(the_tenant_route('voice-parts.index'));

        $this->assertSoftDeleted($voice_part);
    }

    public function test_edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $voice_part = VoicePart::factory()->create();

        $this->get(the_tenant_route('voice-parts.edit', [$voice_part]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('VoiceParts/Edit')
                ->has('voice_part')
            );
    }

    public function test_index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Music Team'));

        $this->get(the_tenant_route('voice-parts.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('VoiceParts/Index')
                ->has('parts')
            );
    }

    #[DataProvider('voicePartProvider')]
    public function test_store_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $data = $getData();
        $response = $this->post(the_tenant_route('voice-parts.store'), $data);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('voice-parts.index'));

        $this->assertDatabaseHas('voice_parts', $data);
    }

    #[DataProvider('voicePartProvider')]
    public function test_update_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $voice_part = VoicePart::factory()->create();

        $data = $getData();
        $this->put(the_tenant_route('voice-parts.update', [$voice_part]), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(the_tenant_route('voice-parts.index'));

        $this->assertDatabaseHas('voice_parts', $data);
    }

    public static function voicePartProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    return [
                        'title' => $faker->word(),
                        'colour' => $faker->hexColor(),
                    ];
                },
            ],
        ];
    }
}
