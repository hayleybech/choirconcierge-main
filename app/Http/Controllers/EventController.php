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
        $all_events = Event::with([])
            ->filter()
            ->get();

        // Sort
        $sort_by = $request->input('sort_by', 'name');
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

        $event = Event::create($request->validated());

        return redirect()->route('events.show', [$event])->with(['status' => 'Event created. ']);
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $my_rsvp = $event->my_rsvp();
        $singers_rsvp_yes_count = $event->singers_rsvp_response('yes')->count();
        $singers_rsvp_maybe_count = $event->singers_rsvp_response('maybe')->count();
        $singers_rsvp_no_count = $event->singers_rsvp_response('no')->count();
        $singers_rsvp_missing_count = $event->singers_rsvp_missing()->count();

        return view('events.show', compact('event', 'my_rsvp', 'singers_rsvp_yes_count', 'singers_rsvp_maybe_count', 'singers_rsvp_no_count', 'singers_rsvp_missing_count' ));
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

        $event->update($request->validated());

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
        ];

        // Merge filters with sort query string
        $url = $request->url() . '?' . Event::getFilterQueryString();

        $current_sort = $request->input('sort_by', 'title');
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
