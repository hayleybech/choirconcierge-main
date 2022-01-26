<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\Singer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AttendanceController
 */
class AttendanceControllerTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/**
	 * @test
	 */
	public function index_returns_an_ok_response(): void
	{
		$this->actingAs($this->createUserWithRole('Events Team'));

		$event = Event::factory()->create();

		$this->get(the_tenant_route('events.attendances.index', [$event]))
		    ->assertOk()
            ->assertInertia(fn(Assert $page) => $page
                ->component('Events/Attendance/Index')
                ->has('event')
                ->has('voice_parts'));
	}

	/**
	 * @test
	 */
	public function update_all_redirects_to_event(): void
	{
		$this->actingAs($this->createUserWithRole('Events Team'));

		$event = Event::factory()->create();
		$singer = Singer::factory()->create();

		$attendance_response = $this->faker->randomElement(['present', 'absent', 'absent_apology']);
		$absent_reason = $this->faker->optional(0.3)->sentence();
		$response = $this->post(the_tenant_route('events.attendances.updateAll', [$event]), [
			'attendance_response' => [
				$singer->id => $attendance_response,
			],
			'absent_reason' => [
				$singer->id => $absent_reason,
			],
		]);

		$response->assertSessionHasNoErrors();
		$response->assertRedirect(the_tenant_route('events.show', ['event' => $event]));
		$this->assertDatabaseHas('attendances', [
			'response' => $attendance_response,
			'absent_reason' => $absent_reason,
			'event_id' => $event->id,
			'singer_id' => $singer->id,
		]);
	}
}
