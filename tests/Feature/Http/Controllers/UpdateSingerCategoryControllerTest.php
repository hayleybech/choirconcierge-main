<?php


namespace Tests\Feature\Http\Controllers;


use App\Models\Role;
use App\Models\Singer;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use Database\Seeders\Dummy\DummyUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateSingerCategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DummyUserSeeder::class);
    }

    /** @test */
    public function update_for_admin_changes_category(): void
    {
        $user = User::withRoles(['Admin'])->first();
        $this->actingAs($user);

        $singer = Singer::query()->inRandomOrder()->first();
        $new_category = SingerCategory::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('singers.categories.update', ['singer' => $singer]), [
            'move_category' => $new_category,
        ] );

        $response->assertRedirect();
        $this->assertDatabaseHas('singers', [
            'singer_category_id' => $new_category->id,
        ]);
    }
}