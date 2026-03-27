<?php

namespace App\Http\Controllers;

use App\CustomSorts\EventTypeSort;
use App\Http\Requests\EventRequest;
use App\Models\Ensemble;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Membership;
use App\Notifications\EventCreated;
use App\Notifications\EventUpdated;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class EventController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Event::class);
    }

    public function index(Request $request): Response
    {
        $totalEnsemblesCount = Ensemble::count();
        $userEnsemblesCount = (auth()->user()?->isSuperAdmin || auth()->user()?->membership?->hasAbility('events_update'))
            ? $totalEnsemblesCount
            : auth()->user()?->membership?->enrolments->count() ?? 0;

        $ensembles = Ensemble::query()
            ->when(!(auth()->user()?->isSuperAdmin || auth()->user()?->membership?->hasAbility('events_update')), function (Builder $query) {
                $query->whereIn('id', auth()->user()?->membership?->enrolments->pluck('ensemble_id') ?? []);
            })
            ->get();

        return Inertia::render('Events/Index', [
            'events' => $this->getEvents(),
            'eventTypes' => EventType::all()->values(),
            'userEnsemblesCount' => $userEnsemblesCount,
            'ensembles' => $ensembles,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Events/Create', [
            'types' => EventType::all()->values(),
            'ensembles' => Ensemble::all()->values(),
        ]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $event = Event::create($request->safe()->except(['send_notification', 'ensembles']));

        if ($request->has('ensembles')) {
            $event->ensembles()->sync($request->input('ensembles'));
        }

        if ($request->input('send_notification')) {
            $notification = new EventCreated($event);
            $recipients = Membership::active()->with('user')->get()->pluck('user');
            Notification::send($recipients, $notification);
            $notification->log($event->id, $recipients->first());
        }

        return redirect()
            ->route('events.show', [$event])
            ->with(['status' => 'Event created. ']);
    }

    public function show(Event $event): Response
    {
        $event->load(['repeat_parent:id,call_time', 'my_attendance', 'activities' => fn($query) => $query->orderBy('order'), 'activities.song'])
            ->append(['in_future', 'is_repeat_parent', 'parent_in_past', 'my_rsvp']);

        $event->can = [
            'update_event' => auth()->user()?->can('update', $event),
            'delete_event' => auth()->user()?->can('delete', $event),
        ];

        return Inertia::render('Events/Show', [
            'event' => $event,
            'rsvpCount' => [
                'yes' => $event->singers_rsvp_response('yes')->count(),
                'no' => $event->singers_rsvp_response('no')->count(),
                'unknown' => $event->singers_rsvp_missing()->count(),
            ],
            'voicePartsRsvpCount' => [
                'yes' => $event->voice_parts_rsvp_response_count('yes')->get(),
            ],
            'attendanceCount' => [
                'present' => $event->singers_attendance('present')->count(),
                'late' => $event->singers_attendance('late')->count(),
                'late_deemed_absent' => $event->singers_attendance('late_deemed_absent')->count(),
                'absent' => $event->singers_attendance('absent')->count(),
                'absent_apology' => $event->singers_attendance('absent_apology')->count(),
                'unknown' => $event->singers_attendance_missing()->count(),
            ],
            'voicePartsAttendanceCount' => [
                'present' => $event->voice_parts_attendance_count('present')->get(),
                'late' => $event->voice_parts_attendance_count('late')->get(),
            ],
            'addToCalendarLinks' => [
                'google' => $event->add_to_calendar_link?->google(),
                'webOutlook' => $event->add_to_calendar_link?->webOutlook(),
                'ics' => $event->add_to_calendar_link?->ics(),
            ],
        ]);
    }

    public function edit(Event $event, Request $request): Response
    {
        return Inertia::render('Events/Edit', [
            'event' => $event,
            'types' => EventType::all()->values(),
            'mode' => $request->input('mode'),
            'ensembles' => Ensemble::all()->values(),
        ]);
    }

    public function update(Event $event, EventRequest $request)
    {
        if ($event->is_repeating) {
            return back()->with(['status' => 'The server tried to edit a repeating event incorrectly.', 'success' => false]);
        }

        $event->fill($request->safe()->except(['send_notification', 'ensembles']));
        $original = $event->getOriginal();
        $event->save();

        if ($request->has('ensembles')) {
            $event->ensembles()->sync($request->input('ensembles'));
        }

        if ($request->input('is_repeating')) {
            $event->createRepeats();
        }

        if ($request->input('send_notification')) {
            $notification = new EventUpdated($event, $original);
            $recipients = Membership::active()->with('user')->get()->pluck('user');
            Notification::send($recipients, $notification);
            $notification->log($event->id, $recipients->first());
        }

        return redirect()
            ->route('events.show', [$event])
            ->with(['status' => 'Event updated. ']);
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()
            ->route('events.index')
            ->with(['status' => 'Event deleted. ']);
    }

    public function clone(Event $event): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $clone = $event
            ->load('type')
            ->replicate()
            ->fill([
                'title' => 'Copy of ' . $event->title,
                'created_at' => now(),
                'update_at' => now(),

                'is_repeating' => false,
                'repeat_parent_id' => null,
                'repeat_frequency_amount' => 0,
                'repeat_frequency_unit' => '',
            ]);

        $clone->type()->associate($event->type);

        $clone->save();

        return redirect()
            ->route('events.show', [$clone])
            ->with(['status' => 'Event cloned. ']);
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $this->authorize('update', Event::class);

        $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:events,id',
            'event_type_id' => 'nullable|exists:event_types,id',
            'ensemble_ids' => 'nullable|array',
            'ensemble_ids.*' => 'exists:ensembles,id',
        ]);

        $eventIds = $request->input('event_ids');

        if ($request->filled('event_type_id')) {
            Event::whereIn('id', $eventIds)->update(['type_id' => $request->event_type_id]);
        }

        if ($request->has('ensemble_ids')) {
            $ensembleIds = $request->input('ensemble_ids');
            foreach ($eventIds as $eventId) {
                (new Event(['id' => $eventId]))->setRawAttributes(['id' => $eventId], true)->ensembles()->sync($ensembleIds);
            }
        }

        return redirect()
            ->route('events.index')
            ->with(['status' => count($eventIds) . ' events updated. ']);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $this->authorize('delete', Event::class);

        $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:events,id',
        ]);

        $eventIds = $request->input('event_ids');

        Event::whereIn('id', $eventIds)->delete();

        return redirect()
            ->route('events.index')
            ->with(['status' => count($eventIds) . ' events deleted.']);
    }

    private function getEvents(): LengthAwarePaginator
    {
        $userEnsembles = auth()->user()?->membership?->enrolments->pluck('ensemble_id');
        $canUpdate = auth()->user()?->membership?->hasAbility('events_update');

        return QueryBuilder::for(Event::class)
            ->when(!$canUpdate && !auth()->user()?->isSuperAdmin, function (Builder $query) use ($userEnsembles) {
                $query->where(function (Builder $query) use ($userEnsembles) {
                    $query->whereDoesntHave('ensembles')
                        ->orWhereHas('ensembles', function (Builder $query) use ($userEnsembles) {
                            $query->whereIn('ensembles.id', $userEnsembles ?? []);
                        });
                });
            })
            ->allowedFilters([
                'title',
                AllowedFilter::exact('type.id'),
                AllowedFilter::scope('date')->default(['upcoming']),
                AllowedFilter::exact('ensembles.id'),
            ])
            ->with(['repeat_parent:id,call_time'])
            ->withCount([
                'rsvps as going_count' => function ($query) {
                    $query->where('response', '=', 'yes');
                },
                'attendances as present_count' => function ($query) {
                    $query->whereIn('response', ['present', 'late']);
                },
            ])
            ->allowedSorts([
                'title',
                'start_date',
                AllowedSort::custom('type-title', new EventTypeSort(), 'type'),
                'created_at',
            ])
            ->defaultSort('start_date')
            ->paginate(50)->appends(request()->query())
            ->through(fn($event) => $event->append(['is_repeat_parent', 'my_rsvp']));
    }
}
