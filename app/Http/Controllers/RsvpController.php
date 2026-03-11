<?php

namespace App\Http\Controllers;

use App\Models\Enrolment;
use App\Models\Ensemble;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Rsvp;
use App\Models\VoicePart;
use App\CustomSorts\SingerNameSort;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class RsvpController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Rsvp::class);
    }

	public function index(Event $event): Response
	{
		$this->authorize('viewAny', Rsvp::class);

        $nameSort = AllowedSort::custom('full-name', new SingerNameSort(), 'name');
        $rsvpSort = AllowedSort::callback('rsvp-response', function (Builder $query, bool $descending) use ($event) {
            $direction = $descending ? 'DESC' : 'ASC';
            $query->select('memberships.*')
                ->selectSub(
                    Rsvp::query()
                        ->select('response')
                        ->whereColumn('membership_id', 'memberships.id')
                        ->where('event_id', $event->id)
                        ->limit(1),
                    'rsvp_response'
                )
                ->orderByRaw("CASE
                    WHEN rsvp_response = 'yes' THEN 1
                    WHEN rsvp_response = 'maybe' THEN 2
                    WHEN rsvp_response IS NULL THEN 3
                    WHEN rsvp_response = 'no' THEN 4
                    ELSE 5
                END $direction");
        });

        $query = Membership::forEvent($event)
            ->with([
                'user',
                'enrolments.voice_part',
                'enrolments.ensemble',
                'rsvps' => fn($query) => $query->where('event_id', '=', $event->id),
            ]);

        $pagination = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::callback('user.name', fn(Builder $query, $value) => $query
                    ->whereHas('user', fn(Builder $query) => $query
                        ->whereRaw('CONCAT(first_name, ?, last_name) LIKE LOWER(?)', [' ', "%$value%"])
                        ->orWhereRaw('email LIKE LOWER(?)', ["%$value%"])
                    )),
                AllowedFilter::callback('enrolments.voice_part_id', fn(Builder $query, $value) => $query
                    ->whereHas('enrolments', fn(Builder $query) => $query
                        ->whereIn('voice_part_id', (array) $value)
                    )
                ),
                AllowedFilter::callback('enrolments.ensemble_id', fn(Builder $query, $value) => $query
                    ->whereHas('enrolments', fn(Builder $query) => $query
                        ->whereIn('ensemble_id', (array) $value)
                    )
                ),
                AllowedFilter::callback('rsvp.response', function (Builder $query, $value) use ($event) {
                    $responses = (array) $value;
                    $query->where(function (Builder $query) use ($responses, $event) {
                        if (in_array('unknown', $responses)) {
                            $query->whereDoesntHave('rsvps', fn($query) => $query->where('event_id', $event->id))
                                  ->orWhereHas('rsvps', fn($query) => $query->where('event_id', $event->id)->whereIn('response', $responses));
                        } else {
                            $query->whereHas('rsvps', fn($query) => $query->where('event_id', $event->id)->whereIn('response', $responses));
                        }
                    });
                }),
            ])
            ->allowedSorts([
                $nameSort,
                $rsvpSort,
            ])
            ->defaultSort($nameSort)
            ->paginate(50)
            ->appends(request()->query());

        $singers = collect($pagination->items())->map(function ($membership) use ($event) {
            $membership->rsvp = $membership->rsvps->first() ?? Rsvp::Null();

            if ($event->ensembles->isNotEmpty()) {
                $membership->setRelation('enrolments', $membership->enrolments->filter(function ($enrolment) use ($event) {
                    return $event->ensembles->contains($enrolment->ensemble_id);
                }));
            }

            return $membership;
        });

		return Inertia::render('Events/Rsvps/Index', [
			'event' => $event->load('ensembles'),
			'allSingers' => $singers,
            'pagination' => $pagination,
            'totalEnsemblesCount' => Ensemble::count(),
            'voiceParts' => VoicePart::all()->values(),
            'ensembles' => Ensemble::ensembleRestricted()->get()->values(),
            'counts' => [
                'yes' => $event->singers_rsvp_response('yes')->count(),
                'maybe' => $event->singers_rsvp_response('maybe')->count(),
                'no' => $event->singers_rsvp_response('no')->count(),
                'unknown' => $event->singers_rsvp_missing()->count(),
            ],
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
