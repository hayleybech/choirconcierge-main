<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\EditRecurringEventController
 */
class EditRecurringEventControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function invoke_redirects_to_correct_edit_page($mode): void
    {
	    $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()
	        ->repeating()
	        ->create();
        self::assertGreaterThan(0, $event->repeat_children()->count());

        $response = $this->get(the_tenant_route('events.edit-recurring', [$event, 'mode' => $mode]));

	    $edit_urls = [
		    'single'    => route('events.edit', ['event' => $event, 'mode' => $mode]),
		    'following' => route('events.edit', ['event' => $event, 'mode' => $mode]),
		    'all'       => route('events.edit', ['event' => $event->repeat_parent, 'mode' => $mode]),
	    ];
        $response->assertRedirect($edit_urls[$mode]);
    }

    public function modeProvider(): array
    {
    	return [
		   'single'     => ['single'],
		   'following'  => ['following'],
		   'all'        => ['all'],
	    ];
    }
}
