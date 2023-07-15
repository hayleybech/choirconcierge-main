<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rsvp;
use App\Models\Membership;
use App\Models\VoicePart;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RsvpController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Rsvp::class);
    }

	public function index(Event $event): Response
	{
		$this->authorize('viewAny', Rsvp::class);

		$singers = Membership::query()
			->with([
				'user',
				'rsvps' => fn($query) => $query->where('event_id', '=', $event->id),
			])
			->get()
            ->map(function ($singer) {
                $singer->rsvp = $singer->rsvps->first() ?? Rsvp::Null();
                return $singer;
            })
			->groupBy('voice_part_id');

		$voice_parts = VoicePart::all()
			->push(VoicePart::getNullVoicePart())
			->map(function ($part) use ($singers) {
				$part->members = $singers[$part->id === null ? "" : $part->id] ?? collect([]);
				return $part;
			});

		return Inertia::render('Events/Rsvps/Index', [
			'event' => $event,
			'voiceParts' => $voice_parts->values(),
		]);
	}

    public function store(Request $request, Event $event): RedirectResponse
    {
        $request->validate(['rsvp_response' => 'required']);

        $event->rsvps()->updateOrCreate(
            ['membership_id' => Auth::user()->membership->id],
            ['response' => $request->input('rsvp_response')]
        );

        return back()->with(['status' => 'RSVP saved.']);
    }

    public function update(Request $request, Event $event, Rsvp $rsvp): RedirectResponse
    {
        $request->validate(['rsvp_response' => 'required']);

        $event->rsvps()->updateOrCreate(
            ['membership_id' => Auth::user()->membership->id],
            ['response' => $request->input('rsvp_response')]
        );

        return back()->with(['status' => 'RSVP saved.']);
    }

    public function destroy(Event $event, Rsvp $rsvp): RedirectResponse
    {
        $rsvp->delete();

        return back()->with(['status' => 'RSVP deleted.']);
    }
}
