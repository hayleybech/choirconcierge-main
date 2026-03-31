<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Membership;
use App\Models\SingerStatus;
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

        $newStatusId = SingerStatus::where('id', '!=', $singer->status->id)->inRandomOrder()->value('id');
        $response = $this->get(
            the_tenant_route('singers.statuses.update', [$singer]).'?move_status='.$newStatusId,
            [
                'move_status' => $newStatusId,
            ],
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('membership_singer_status', [
            'membership_id' => $singer->id,
            'singer_status_id' => $newStatusId,
        ]);

        $this->assertEquals($newStatusId, $singer->fresh()->status->id);
    }
}
