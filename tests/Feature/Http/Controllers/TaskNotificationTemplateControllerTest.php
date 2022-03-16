<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\NotificationTemplate;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TaskNotificationTemplateController
 */
class TaskNotificationTemplateControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()->create();

        $this->get(the_tenant_route('tasks.notifications.create', [$task]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tasks/Notifications/Create')
                ->has('task')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_task(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()
            ->has(NotificationTemplate::factory(), 'notification_templates')
            ->create();

        $this->delete(the_tenant_route('tasks.notifications.destroy', [
            $task,
            'notification' => $task->notification_templates->first(),
        ]))
            ->assertRedirect(the_tenant_route('tasks.show', $task));

        $this->assertSoftDeleted($task->notification_templates->first());
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()
            ->has(NotificationTemplate::factory(), 'notification_templates')
            ->create();

        $this->get(
            the_tenant_route('tasks.notifications.edit', [
                $task,
                'notification' => $task->notification_templates->first(),
            ]),
        )->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tasks/Notifications/Edit')
                ->has('task')
                ->has('notification')
            );
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()
            ->has(NotificationTemplate::factory(), 'notification_templates')
            ->create();

        $this->get(
            the_tenant_route('tasks.notifications.show', [
                $task,
                'notification' => $task->notification_templates->first(),
            ]),
        )->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tasks/Notifications/Show')
                ->has('task')
                ->has('notification')
            );
    }

    /**
     * @test
     */
    public function store_redirects_to_show(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()->create();

        $data = [
            'subject' => $this->faker->sentence(),
            'recipients' => 'role:1',
            'body' => $this->faker->paragraph(),
            'delay' => $this->faker->numberBetween(2, 50).
                ' '.
                $this->faker->randomElement(['seconds', 'minutes', 'hours', 'days', 'weeks', 'months']),
        ];
        $response = $this->post(the_tenant_route('tasks.notifications.store', [$task]), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas(
            'notification_templates',
            array_merge($data, [
                'task_id' => $task->id,
            ]),
        );
        $notification_template = NotificationTemplate::firstWhere('subject', $data['subject']);
        $response->assertRedirect(the_tenant_route('tasks.notifications.show', [$task, $notification_template]));
    }

    /**
     * @test
     */
    public function update_redirects_to_show(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $task = Task::factory()
            ->has(NotificationTemplate::factory(), 'notification_templates')
            ->create();

        $data = [
            'subject' => $this->faker->sentence(),
            'recipients' => 'role:1',
            'body' => $this->faker->paragraph(),
            'delay' => $this->faker->numberBetween(2, 50).
                ' '.
                $this->faker->randomElement(['seconds', 'minutes', 'hours', 'days', 'weeks', 'months']),
        ];
        $response = $this->put(
            the_tenant_route('tasks.notifications.update', [
                $task,
                'notification' => $task->notification_templates->first(),
            ]),
            $data,
        );

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'notification_templates',
            array_merge($data, [
                'task_id' => $task->id,
            ]),
        );
        $response->assertRedirect(route('tasks.notifications.show', [$task, $task->notification_templates->first()]));
    }
}
