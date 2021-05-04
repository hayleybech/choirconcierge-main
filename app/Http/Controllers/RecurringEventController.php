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
		$edit_urls = [
			'single'    => route('events.edit', ['event' => $event, 'mode' => $mode]),
			'following' => route('events.edit', ['event' => $event, 'mode' => $mode]),
			'all'       => route('events.edit', ['event' => $event->repeat_parent, 'mode' => $mode]),
		];
		return redirect($edit_urls[$mode]);
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

		if($mode === 'single') {
			$event->updateSingle(Arr::except($request->validated(), 'send_notification'));
		} elseif ($mode === 'all') {
			$event->updateAll($request->validated());
		} elseif ($mode === 'following') {
			$event->updateFollowing(Arr::except($request->validated(), 'send_notification'));
		} else {
			abort(500, 'The server failed to determine the edit mode on the repeating event.');
		}

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

		if('single' === $mode) {
			$event->delete_single();
		} elseif('following' === $mode) {
			$event->delete_with_following();
		} elseif('all' === $mode) {
			$this->authorize('delete', $event->repeat_parent);
			$event->repeat_parent->delete_with_all();
		}

		return redirect()->route('events.index')->with(['status' => 'Event(s) deleted. ', ]);
	}
}
