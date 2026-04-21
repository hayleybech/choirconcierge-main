<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Membership;
use App\Enums\SingerStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UpdateSingerStatusController
 */
class UpdateSingerStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Membership::factory()->create();

        $newStatus = SingerStatus::PROSPECTS->value;
        $response = $this->get(
            the_tenant_route('singers.statuses.update', [$singer]).'?move_status='.$newStatus,
            [
                'move_status' => $newStatus,
            ],
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('membership_status', [
            'membership_id' => $singer->id,
            'status' => $newStatus,
        ]);

        $this->assertEquals($newStatus, $singer->fresh()->status->value);
    }
}
