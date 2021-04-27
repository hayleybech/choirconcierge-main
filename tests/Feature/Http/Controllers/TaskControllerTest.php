<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TaskController
 */
class TaskControllerTest extends TestCase
{
	use WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Admin'));

        $response = $this->get(the_tenant_route('tasks.create'));

        $response->assertOk();
        $response->assertViewIs('tasks.create');
        $response->assertViewHas('roles_keyed');
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
	    $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()->create();

        $response = $this->delete(the_tenant_route('tasks.destroy', [$task]));

	    $this->assertSoftDeleted($task);
	    $response->assertRedirect(the_tenant_route('tasks.index'));
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Admin'));

        $response = $this->get(the_tenant_route('tasks.index'));

        $response->assertOk();
        $response->assertViewIs('tasks.index');
        $response->assertViewHas('tasks');
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()->create();

        $response = $this->get(the_tenant_route('tasks.show', [$task]));

        $response->assertOk();
        $response->assertViewIs('tasks.show');
        $response->assertViewHas('task');
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function store_returns_an_ok_response($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Admin'));

	    $data = $getData();
        $response = $this->post(the_tenant_route('tasks.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tasks', $data);

        $task = Task::firstWhere('name', $data['name']);
        $response->assertRedirect(the_tenant_route('tasks.show', $task));
    }

	public function eventProvider(): array
	{
		return [
			[
				function() {
					$this->setUpFaker();
					return [
						'name'          => $this->faker->sentence(3),
						'role_id'       => Role::where('name', 'Music Team')->value('id'),
						'type'          => 'manual',
						'route'         => 'task.complete',
					];
				}
			]
		];
	}
}
