<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Singer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Event::class);

        // Base query
        $all_events = Event::with(['repeat_parent:id,start_date'])
            ->withCount([
                'rsvps as going_count' => function($query) {
                    $query->where('response', '=', 'yes');
                },
                'attendances as present_count' => function($query) {
                    $query->where('response', '=', 'present');
                },
            ])
            ->filter()
            ->get();

        // Sort
        $sort_by = $request->input('sort_by', 'start_date');
        $sort_dir = $request->input('sort_dir', 'asc');

        // Flip direction for date (so we sort by smallest age not smallest timestamp)
        if($sort_by === 'created_at') $sort_dir = ($sort_dir === 'asc') ? 'desc' : 'asc';

        if( $sort_dir === 'asc') {
            $all_events = $all_events->sortBy($sort_by);
        } else {
            $all_events = $all_events->sortByDesc($sort_by);
        }

        return view('events.index', [
            'all_events'      => $all_events,
            'upcoming_events' => $all_events->where('start_date', '>', now()),
            'past_events'     => $all_events->where('start_date', '<', now()),
            'filters'         => Event::getFilters(),
            'sorts'           => $sorts = $this->getSorts($request),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        $types = EventType::all();

        return view('events.create', compact( 'types') );
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $event = Event::create(
            attributes: collect($request->validated())->except('send_notification')->toArray(),
            send_notification: $request->input('send_notification')
        );

        return redirect()->route('events.show', [$event])->with(['status' => 'Event created. ']);
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load('repeat_parent:id,start_date');

        return view('events.show', [
            'event'   => $event,
            'my_rsvp' => $event->my_rsvp(),
            'my_attendance' => $event->my_attendance(),
            'singers_rsvp_yes_count'     => $event->singers_rsvp_response('yes')->count(),
            'singers_rsvp_maybe_count'   => $event->singers_rsvp_response('maybe')->count(),
            'singers_rsvp_no_count'      => $event->singers_rsvp_response('no')->count(),
            'singers_rsvp_missing_count' => $event->singers_rsvp_missing()->count(),
            'voice_parts_rsvp_yes_count' => $event->voice_parts_rsvp_response_count('yes'),
            'singers_attendance_present' => $event->singers_attendance('present')->count(),
            'singers_attendance_absent'  => $event->singers_attendance('absent')->count(),
            'singers_attendance_absent_apology'  => $event->singers_attendance('absent_apology')->count(),
            'singers_attendance_missing' => $event->singers_attendance_missing()->count(),
            'voice_parts_attendance'     => $event->voice_parts_attendance_count('present'),
        ]);
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        $types = EventType::all();

        return view('events.edit', compact('event',  'types'));
    }

    public function update(Event $event, EventRequest $request): RedirectResponse
    {
        $this->authorize('update', $event);

        $event->update($request->validated(), ['edit_mode' => $request->get('edit_mode')]);

        return redirect()->route('events.show', [$event])->with(['status' => 'Event updated. ', ]);
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')->with(['status' => 'Event deleted. ', ]);
    }


    public function getSorts(Request $request): array
    {
        $sort_cols = [
            'title',
            'type.title',
            'created_at',
            'start_date',
        ];

        // Merge filters with sort query string
        $url = $request->url() . '?' . Event::getFilterQueryString();

        $current_sort = $request->input('sort_by', 'start_date');
        $current_dir =  $request->input('sort_dir', 'asc');

        $sorts = [];
        foreach($sort_cols as $col) {
            // If current sort
            if( $col === $current_sort ) {
                // Create link for opposite sort direction
                $current = true;
                $dir = ( 'asc' === $current_dir ) ? 'desc' : 'asc';
            } else {
                $current = false;
                $dir = 'asc';
            };
            $sorts[$col] = [
                'url'       => $url . "&sort_by=$col&sort_dir=$dir",
                'dir'       => $current_dir,
                'current'   => $current,
            ];
        }
        return $sorts;
    }
}
