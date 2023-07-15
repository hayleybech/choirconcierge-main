<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Membership;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CompleteSingerTaskController
 */
class CompleteSingerTaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function invoke_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $task = Task::factory()->create();
        $singer = Membership::factory()->create(['onboarding_enabled' => true]);
        $singer->initOnboarding();
        $singer->save();

        $response = $this->get(the_tenant_route('task.complete', [$singer, $task]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('singers.index'));

        $this->assertDatabaseHas('memberships_tasks', [
            'membership_id' => $singer->id,
            'task_id' => $task->id,
            'completed' => true,
        ]);
    }
}
