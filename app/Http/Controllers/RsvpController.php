<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rsvp;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
	public function store(Request $request, Event $event): RedirectResponse
	{
		$this->authorize('create', Rsvp::class);

		$request->validate([
			'rsvp_response' => 'required',
		]);

		$event->rsvps()->create([
			'response' => $request->input('rsvp_response'),
			'singer_id' => Auth::user()->singer->id,
		]);

		return back()->with(['status' => 'RSVP saved.']);
	}

	public function update(Request $request, Event $event, Rsvp $rsvp): RedirectResponse
	{
		$this->authorize('update', $rsvp);

		$request->validate([
			'rsvp_response' => 'required',
		]);

		$rsvp->update([
			'response' => $request->input('rsvp_response'),
		]);

		return back()->with(['status' => 'RSVP saved.']);
	}

	public function destroy(Event $event, Rsvp $rsvp): RedirectResponse
	{
		$this->authorize('delete', $rsvp);

		$rsvp->delete();

		return back()->with(['status' => 'RSVP deleted.']);
	}
}
