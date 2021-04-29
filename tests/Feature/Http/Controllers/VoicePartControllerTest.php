<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $response = $this->get(the_tenant_route('voice-parts.create'));

        $response->assertOk();
        $response->assertViewIs('voice-parts.create');
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
	    $this->actingAs($this->createUserWithRole('Admin'));

        $voice_part = VoicePart::factory()->create();

        $response = $this->delete(the_tenant_route('voice-parts.destroy', [$voice_part]));

        $response->assertRedirect(the_tenant_route('voice-parts.index'));
        $this->assertSoftDeleted($voice_part);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Admin'));

        $voice_part = VoicePart::factory()->create();

        $response = $this->get(the_tenant_route('voice-parts.edit', [$voice_part]));

        $response->assertOk();
        $response->assertViewIs('voice-parts.edit');
        $response->assertViewHas('voice_part');
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $response = $this->get(the_tenant_route('voice-parts.index'));

        $response->assertOk();
        $response->assertViewIs('voice-parts.index');
	    $response->assertViewHas('parts');
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Music Team'));

        $voice_part = VoicePart::factory()->create();

        $response = $this->get(the_tenant_route('voice-parts.show', [$voice_part]));

        $response->assertOk();
        $response->assertViewIs('voice-parts.show');
	    $response->assertViewHas('voice_part');
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

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('voice_parts', $data);

        $voice_part = VoicePart::firstWhere('title', $data['title']);
        $response->assertRedirect(the_tenant_route('voice-parts.show', $voice_part));
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
        $response = $this->put(the_tenant_route('voice-parts.update', [$voice_part]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('voice_parts', $data);
        $response->assertRedirect(the_tenant_route('voice-parts.show', $voice_part));
    }

	public function voicePartProvider(): array
	{
		return [
			[
				function() {
					$this->setUpFaker();
					return [
						'title'     => $this->faker->word(),
						'colour'    => $this->faker->hexColor(),
					];
				}
			]
		];
	}
}
