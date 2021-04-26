<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\Dummy\DummyTaskSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DummyTaskSeeder::class);
    }

    /** @test */
    public function index_for_admin_returns_list_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.index');
    }

    /** @test */
    public function create_for_admin_returns_create_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('tasks.create'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.create');
    }

    /** @test */
    public function store_for_admin_creates_a_task(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $data = [
            'name' => $this->faker->sentence,
            'role_id' => Role::query()->inRandomOrder()->first()->id,
            'type' => 'manual',
            'route' => 'task.complete',
        ];
        $response = $this->post(the_tenant_route('tasks.store'), $data);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', $data);
    }

    /** @test */
    public function show_for_admin_returns_show_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $task = Task::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('tasks.show', $task));

        $response->assertViewIs('tasks.show');
        $response->assertOk();
    }

    /** @test */
    public function destroy_for_admin_soft_deletes_task(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $task = Task::query()->inRandomOrder()->first();
        $response = $this->delete(the_tenant_route('tasks.destroy', ['task' => $task]));

        $response->assertRedirect();
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}
