<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        // Base query
        $events = Event::with([])
            ->filter()
            ->get()
            ->groupBy(static function($item, $key){
                return $item['start_date'] > now();
            });
        $past_events = $events[0];
        $upcoming_events = $events[1];

        // Sort
        $sort_by = $request->input('sort_by', 'name');
        $sort_dir = $request->input('sort_dir', 'asc');

        // Flip direction for date (so we sort by smallest age not smallest timestamp)
        if($sort_by === 'created_at') $sort_dir = ($sort_dir === 'asc') ? 'desc' : 'asc';

        if( $sort_dir === 'asc') {
            $events = $events->sortBy($sort_by);
        } else {
            $events = $events->sortByDesc($sort_by);
        }

        $sorts = $this->getSorts($request);

        $filters = Event::getFilters();
        return view('events.index', compact('upcoming_events', 'past_events', 'filters', 'sorts') );
    }

    public function create(): View
    {
        $types = EventType::all();

        return view('events.create', compact( 'types') );
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $event = Event::create($request->validated());

        return redirect()->route('events.show', [$event])->with(['status' => 'Event created. ']);
    }

    public function show(Event $event): View
    {
        return view('events.show', compact('event' ));
    }

    public function edit(Event $event): View
    {
        $types = EventType::all();

        return view('events.edit', compact('event',  'types'));
    }

    public function update(Event $event, EventRequest $request): RedirectResponse
    {
        $event->update($request->validated());

        return redirect()->route('events.show', [$event])->with(['status' => 'Event updated. ', ]);
    }

    public function delete(Event $event): RedirectResponse
    {
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
