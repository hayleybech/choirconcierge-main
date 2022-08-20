<?php

namespace App\Http\Controllers;

use App\CustomSorts\EventTypeSort;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Singer;
use App\Notifications\EventCreated;
use App\Notifications\EventUpdated;
use Illuminate\Database\Eloquent\Collection;
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
        return Inertia::render('Events/Index', [
            'events' => $this->getEvents()->values(),
            'eventTypes' => EventType::all()->values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Events/Create', [
            'types' => EventType::all()->values(),
        ]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $event = Event::create($request->safe()->except('send_notification'));

        if ($request->input('send_notification')) {
            Notification::send(Singer::active()->with('user')->get()->pluck('user'), new EventCreated($event));
        }

        return redirect()
            ->route('events.show', [$event])
            ->with(['status' => 'Event created. ']);
    }

    public function show(Event $event): Response
    {
        $event->load(['repeat_parent:id,call_time', 'my_attendance', 'activities' => fn ($query) => $query->orderBy('order'), 'activities.song'])
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

    public function edit(Event $event): Response
    {
        return Inertia::render('Events/Edit', [
            'event' => $event,
            'types' => EventType::all()->values(),
        ]);
    }

    public function update(Event $event, EventRequest $request): RedirectResponse
    {
        if ($event->is_repeating) {
            return back()->with(['status' => 'The server tried to edit a repeating event incorrectly.', 'success' => false]);
        }

        $event->update($request->safe()->except('send_notification'));

        if ($request->input('send_notification')) {
            Notification::send(Singer::active()->with('user')->get()->pluck('user'), new EventUpdated($event));
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

    private function getEvents(): Collection
    {
        return QueryBuilder::for(Event::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('type.id'),
                AllowedFilter::scope('date')->default(['upcoming']),
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
            ->get();
    }
}
