<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeleteRecurringEventController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Event $event
     * @param String $mode 'single'|'following'|'all'
     * @return RedirectResponse
     */
    public function __invoke(Request $request, Event $event, String $mode): RedirectResponse
    {
        $this->authorize('delete', $event);

        // No point being here if the event isn't repeating
        if( ! $event->is_repeating) {
            abort(405, 'This action is intended for repeating events only.');
        }

        if('single' === $mode) {
            // @todo: re-assign parent if target is parent
            $event->delete();
        } elseif('following' === $mode) {
            $event->delete_with_following();
        } elseif('all' === $mode) {
            $this->authorize('delete', $event->repeat_parent);
            $event->repeat_parent->delete_with_all();
        }

        return redirect()->route('events.index')->with(['status' => 'Event(s) deleted. ', ]);
    }
}
