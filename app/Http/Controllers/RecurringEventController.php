<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EventUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

class RecurringEventController extends Controller
{
	/**
	 * @param Request $request
	 * @param Event $event
	 * @param String $mode 'single'|'following'|'all'
	 * @return RedirectResponse
	 */
	public function edit(Request $request, Event $event, String $mode): RedirectResponse
	{
		$url = match($mode) {
			'single'    => route('events.edit', ['event' => $event, 'mode' => 'single']),
			'following' => route('events.edit', ['event' => $event, 'mode' => 'following']),
			'all'       => route('events.edit', ['event' => $event->repeat_parent, 'mode' => $mode]),
		};
		return redirect($url);
	}

	/**
	 * @param Event $event
	 * @param EventRequest $request
	 * @param String $mode 'single'|'following'|'all'
	 * @return RedirectResponse
	 */
	public function update(Event $event, EventRequest $request, String $mode): RedirectResponse
	{
		$this->authorize('update', $event);

		if(! $event->is_repeating) {
			return back()->with(['error' => 'The server tried to edit a non-repeating event incorrectly.']);
		}

		match($mode) {
			'single'    => $event->updateSingle(Arr::except($request->validated(), 'send_notification')),
			'all'       => $event->updateAll($request->validated()),
			'following' => $event->updateFollowing(Arr::except($request->validated(), 'send_notification')),
			default     => abort(500, 'The server failed to determine the edit mode on the repeating event.'),
		};

		$request->whenHas('send_notification', fn() => Notification::send(User::active()->get(), new EventUpdated($event)));

		return redirect()->route('events.show', [$event])->with(['status' => 'Event updated. ', ]);
	}

	/**
	 * @param Request $request
	 * @param Event $event
	 * @param String $mode 'single'|'following'|'all'
	 * @return RedirectResponse
	 */
	public function destroy(Request $request, Event $event, String $mode): RedirectResponse
	{
		$this->authorize('delete', $event);

		// No point being here if the event isn't repeating
		abort_if(! $event->is_repeating, 403, 'This action is intended for repeating events only.');

		match($mode) {
			'single'        => $event->delete_single(),
			'following'     => $event->delete_with_following(),
			'all'           => $event->repeat_parent->delete_with_all(),
		};

		return redirect()->route('events.index')->with(['status' => 'Event(s) deleted. ', ]);
	}
}
