<?php


namespace Tests\Feature\Http\Controllers;


use App\Models\Role;
use App\Models\User;
use App\Models\VoicePart;
use Database\Seeders\Dummy\DummyUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoicePartControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DummyUserSeeder::class);
    }

    /** @test */
    public function index_for_employee_returns_list_view(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('voice-parts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('voice-parts.index');
    }

    /** @test */
    public function create_for_music_team_returns_create_view(): void
    {
        $user = User::withRoles(['Music Team'])
            ->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('voice-parts.create'));

        $response->assertStatus(200);
        $response->assertViewIs('voice-parts.create');
    }

    /** @test */
    public function store_for_music_team_creates_a_voice_part(): void
    {
        $user = User::withRoles(['Music Team'])
            ->first();
        $this->actingAs($user);

        $data  = [
            'title' => $this->faker->word,
            'colour' => $this->faker->hexColor,
        ];
        $response = $this->post(the_tenant_route('voice-parts.store'), $data);

        $response->assertRedirect();

        $this->assertDatabaseHas('voice_parts', $data);
    }

    /** @test */
    public function edit_for_music_team_returns_edit_view(): void
    {
        $user = User::withRoles(['Music Team'])->first();
        $this->actingAs($user);

        $voice_part = VoicePart::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('voice-parts.edit', ['voice_part' => $voice_part]) );

        $response->assertOk();
        $response->assertViewIs('voice-parts.edit');
    }

    /** @test */
    public function update_for_music_team_changes_voice_part(): void
    {
        $user = User::withRoles(['Music Team'])->first();
        $this->actingAs($user);

        $data  = [
            'title' => $this->faker->word,
            'colour' => $this->faker->hexColor,
        ];
        $voice_part = VoicePart::query()->inRandomOrder()->first();
        $response = $this->put( the_tenant_route('voice-parts.update', ['voice_part' => $voice_part]), $data );

        $response->assertRedirect();
        $this->assertDatabaseHas('voice_parts', $data);
    }

    /** @test */
    public function destroy_for_admin_soft_deletes_voice_part(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $voice_part = VoicePart::query()->inRandomOrder()->first();
        $response = $this->delete( the_tenant_route('voice-parts.destroy', ['voice_part' => $voice_part]) );

        $response->assertRedirect();
        $this->assertSoftDeleted('voice_parts', ['id' => $voice_part->id]);
    }
}