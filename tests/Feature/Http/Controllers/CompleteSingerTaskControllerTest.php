<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Singer;
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
		$singer = Singer::factory()->create(['onboarding_enabled' => true]);

		$response = $this->get(the_tenant_route('task.complete', [$singer, $task]));
		$response->assertSessionHasNoErrors();
		$response->assertRedirect(the_tenant_route('singers.index'));

		$this->assertDatabaseHas('singers_tasks', [
			'singer_id' => $singer->id,
			'task_id' => $task->id,
			'completed' => true,
		]);
	}
}
