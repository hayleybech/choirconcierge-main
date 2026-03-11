<?php

namespace App\Http\Controllers;

use App\Models\Enrolment;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Rsvp;
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

		$singers = Membership::forEvent($event)
            ->with([
                'user',
                'enrolments.voice_part',
                'enrolments.ensemble',
                'rsvps' => fn($query) => $query->where('event_id', '=', $event->id),
            ])
			->get()
            ->map(function ($membership) use ($event) {
                $membership->rsvp = $membership->rsvps->first() ?? Rsvp::Null();

                if ($event->ensembles->isNotEmpty()) {
                    $membership->setRelation('enrolments', $membership->enrolments->filter(function ($enrolment) use ($event) {
                        return $event->ensembles->contains($enrolment->ensemble_id);
                    }));
                }

                return $membership;
            })
            ->sortBy('user.name')
            ->values();

		return Inertia::render('Events/Rsvps/Index', [
			'event' => $event->load('ensembles'),
			'singers' => $singers,
            'totalEnsemblesCount' => \App\Models\Ensemble::count(),
		]);
	}

    public function store(Request $request, Event $event): RedirectResponse
    {
        $request->validate(['rsvp_response' => 'required']);

        if ($event->ensembles->isNotEmpty() && !$event->relevant_memberships()->where('memberships.id', Auth::user()->membership->id)->exists()) {
            abort(403, 'You are not eligible to RSVP for this event.');
        }

        $event->rsvps()->updateOrCreate(
            ['membership_id' => Auth::user()->membership->id],
            ['response' => $request->input('rsvp_response')]
        );

        return back()->with(['status' => 'RSVP saved.']);
    }

    public function update(Request $request, Event $event, Rsvp $rsvp): RedirectResponse
    {
        $request->validate(['rsvp_response' => 'required']);

        if ($event->ensembles->isNotEmpty() && !$event->relevant_memberships()->where('memberships.id', Auth::user()->membership->id)->exists()) {
            abort(403, 'You are not eligible to RSVP for this event.');
        }

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
