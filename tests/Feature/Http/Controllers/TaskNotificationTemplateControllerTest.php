<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\NotificationTemplate;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\Dummy\DummyNotificationTemplateSeeder;
use Database\Seeders\Dummy\DummyTaskSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskNotificationTemplateControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DummyTaskSeeder::class);
        $this->seed(DummyNotificationTemplateSeeder::class);
    }

    /** @test */
    public function create_for_admin_returns_create_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $task = Task::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('tasks.notifications.create', ['task' => $task]));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.notifications.create');
    }

    /** @test */
    public function store_for_admin_creates_a_template(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $task = Task::query()->inRandomOrder()->first();
        $data = [
            'subject' => $this->faker->sentence,
            'recipients' => 'singer:0',
            'body' => $this->faker->paragraph,
            'delay' => '1 second',
        ];
        $response = $this->post(the_tenant_route('tasks.notifications.store', ['task' => $task]), $data);

        $response->assertRedirect();

        $this->assertDatabaseHas('notification_templates', $data);
    }

    /** @test */
    public function show_for_admin_returns_show_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $template = NotificationTemplate::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('tasks.notifications.show', ['task' => $template->task, 'notification' => $template]));

        $response->assertViewIs('tasks.notifications.show');
        $response->assertOk();
    }

    /** @test */
    public function destroy_for_admin_soft_deletes_template(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $template = NotificationTemplate::query()->inRandomOrder()->first();
        $response = $this->delete(the_tenant_route('tasks.notifications.destroy', ['task' => $template->task, 'notification' => $template]));

        $response->assertRedirect();
        $this->assertSoftDeleted('notification_templates', ['id' => $template->id]);
    }
}
