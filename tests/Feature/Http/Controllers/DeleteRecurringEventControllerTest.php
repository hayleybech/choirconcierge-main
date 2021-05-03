<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DeleteRecurringEventController
 */
class DeleteRecurringEventControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function invoke_for_single_deletes_one(): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

        $parent = Event::factory()
	        ->repeating()
	        ->create();

        $first_child = $parent->repeat_children->first();

        $response = $this->get(the_tenant_route('events.delete-recurring', [$parent, 'mode' => 'single']));
        $response->assertRedirect(the_tenant_route('events.index'));

        // Assert parent deleted
	    $this->assertSoftDeleted($parent);

	    // Assert children still exist
        $this->assertDatabaseHas('events', [
        	'id'            => $first_child->id,
        	'repeat_until'  => tz_from_tenant_to_utc($parent->repeat_until)->format('Y-m-d H:i:s'),
        ]);

        // Assert parent ID changed
	    $first_child->refresh();
	    self::assertEquals($first_child->id, $first_child->repeat_parent_id);
    }

	/**
	 * @test
	 */
	public function invoke_for_all_deletes_all(): void
	{
		$this->actingAs($this->createUserWithRole('Events Team'));

		$parent = Event::factory()
			->repeating()
			->create();

		$first_child = $parent->repeat_children->first();
		$last_child = $parent->repeat_children->last();

		$response = $this->get(the_tenant_route('events.delete-recurring', [$parent, 'mode' => 'all']));
		$response->assertRedirect(the_tenant_route('events.index'));

		// Assert parent deleted
		$this->assertSoftDeleted($parent);

		// Assert children deleted
		$this->assertSoftDeleted($first_child);
		$this->assertSoftDeleted($last_child);
	}

	/**
	 * @test
	 */
	public function invoke_for_following_deletes_some(): void
	{
		$this->actingAs($this->createUserWithRole('Events Team'));

		$parent = Event::factory()
			->repeating()
			->create();

		$target = $parent->repeat_children[round($parent->repeat_children->count() / 2)];

		$prev_sibling = $target->prevRepeat();
		$next_sibling = $target->nextRepeat();
		$last_sibling = $parent->repeat_children->last();

		$response = $this->get(the_tenant_route('events.delete-recurring', [$target, 'mode' => 'following']));
		$response->assertRedirect(the_tenant_route('events.index'));

		// Assert target and following siblings deleted
		$this->assertSoftDeleted($target);
		$this->assertSoftDeleted($next_sibling);
		$this->assertSoftDeleted($last_sibling);

		// Assert earlier events intact
		$this->assertNotSoftDeleted($parent);
		$this->assertNotSoftDeleted($prev_sibling);

		// Assert series updated
		$parent->refresh();
		self::assertEquals($prev_sibling->call_time, $parent->repeat_until);
	}
}
