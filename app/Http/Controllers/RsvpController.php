<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rsvp;
use App\Models\Singer;
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

//		$event->load(['attendances' => fn ($query) => $query->with('singer.user')->whereHas('singer', fn ($query) => $query->active())]);
//
//		$event->attendances->each(fn ($attendance) => $attendance->singer->user->append('avatar_url'));

		// get all singers
			// with voice part
			// with rsvp for this event
			// replace no voice part with NullVoicePart
		// group by voice part

		$singers = Singer::query()
			->with([
				'user',
				'rsvps' => fn($query) => $query->where('event_id', '=', $event->id),
			])
			->get()
			->groupBy('voice_part_id');

		$voice_parts = VoicePart::all();

//
//		$voice_parts = VoicePart::all()
//			->push(VoicePart::getNullVoicePart())
//			->map(function ($part) use ($event) {
//				$part->singers = $event->attendances
//					->filter(fn ($attendance) => $attendance->singer->voice_part_id === $part->id)
//					->values();
//
//				return $part;
//			});

		return Inertia::render('Events/Rsvps/Index', [
			'event' => $event,
			'singers' => $singers->values(),
			'voiceParts' => $voice_parts->values(),
		]);
	}

    public function store(Request $request, Event $event): RedirectResponse
    {
        $request->validate(['rsvp_response' => 'required']);

        $event->rsvps()->updateOrCreate(
            ['singer_id' => Auth::user()->singer->id],
            ['response' => $request->input('rsvp_response')]
        );

        return back()->with(['status' => 'RSVP saved.']);
    }

    public function update(Request $request, Event $event, Rsvp $rsvp): RedirectResponse
    {
        $request->validate(['rsvp_response' => 'required']);

        $event->rsvps()->updateOrCreate(
            ['singer_id' => Auth::user()->singer->id],
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
