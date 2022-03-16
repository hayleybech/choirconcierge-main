<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
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

        $this->get(the_tenant_route('tasks.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tasks/Create')
                ->has('roles')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()->create();

        $this->delete(the_tenant_route('tasks.destroy', [$task]))
            ->assertRedirect(the_tenant_route('tasks.index'));

        $this->assertSoftDeleted($task);
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('tasks.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tasks/Index')
                ->has('tasks')
            );
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()->create();

        $this->get(the_tenant_route('tasks.show', [$task]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tasks/Show')
                ->has('task')
            );
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function store_returns_an_ok_response($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $data = $getData();
        $response = $this->post(the_tenant_route('tasks.store'), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tasks', $data);

        $task = Task::firstWhere('name', $data['name']);
        $response->assertRedirect(the_tenant_route('tasks.show', $task));
    }

    public function eventProvider(): array
    {
        return [
            [
                function () {
                    $this->setUpFaker();

                    return [
                        'name' => $this->faker->sentence(3),
                        'role_id' => Role::where('name', 'Music Team')->value('id'),
                        'type' => 'manual',
                        'route' => 'task.complete',
                    ];
                },
            ],
        ];
    }
}
