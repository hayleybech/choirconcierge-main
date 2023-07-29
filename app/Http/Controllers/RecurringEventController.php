<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\Membership;
use App\Notifications\EventUpdated;
use App\Services\DeleteRecurringEvent;
use App\Services\UpdateRecurringEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

class RecurringEventController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Event::class);
    }

    /**
     * @param Request $request
     * @param Event $event
     * @param string $mode 'single'|'following'|'all'
     * @return RedirectResponse
     */
    public function edit(Request $request, Event $event, string $mode): RedirectResponse
    {
        $url = match ($mode) {
            'single' => route('events.edit', ['event' => $event, 'mode' => 'single']),
            'following' => route('events.edit', ['event' => $event, 'mode' => 'following']),
            'all' => route('events.edit', ['event' => $event->repeat_parent, 'mode' => $mode])
        };

        return redirect($url);
    }

    /**
     * @param Event $event
     * @param EventRequest $request
     * @param string $mode 'single'|'following'|'all'
     * @return RedirectResponse
     */
    public function update(Event $event, EventRequest $request, string $mode): RedirectResponse
    {
        if (! $event->is_repeating) {
            return back()->with(['error' => 'The server tried to edit a non-repeating event incorrectly.']);
        }

        UpdateRecurringEvent::handle($mode, $event, Arr::except($request->validated(), 'send_notification'));

        if ($request->input('send_notification')) {
            Notification::send(
                Membership::active()->with('user')->get()->map(fn ($singer) => $singer->user),
                new EventUpdated($event)
            );
        }

        return redirect()
            ->route('events.show', [$event])
            ->with(['status' => 'Event updated. ']);
    }

    /**
     * @param Request $request
     * @param Event $event
     * @param string $mode 'single'|'following'|'all'
     * @return RedirectResponse
     */
    public function destroy(Request $request, Event $event, string $mode): RedirectResponse
    {
        // No point being here if the event isn't repeating
        abort_if(! $event->is_repeating, 403, 'This action is intended for repeating events only.');

        DeleteRecurringEvent::handle($mode, $event);

        return redirect()
            ->route('events.index')
            ->with(['status' => 'Event(s) deleted. ']);
    }
}
