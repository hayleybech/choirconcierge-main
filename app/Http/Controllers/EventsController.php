<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventsController extends Controller
{
    public function index(Request $request): View
    {
        // Base query
        $events = Event::with([]);

        // Filter events
        $where = [];
        $filters = $this->getFilters($request);

        // Filter by type
        if($filters['type']['current'] !== 0) {
            $where[] = ['type_id', '=', $filters['type']['current']];
        }
        $events = $events->where($where);

        // Finish and fetch
        $events = $events->get();

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

        return view('events.index', compact('events', 'filters', 'sorts') );
    }

    public function create(): View
    {
        $types = EventType::all();

        return view('events.create', compact( 'types') );
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'             => 'required|max:255',
            'type'              => 'required|exists:event_types,id',
            'start_date'        => 'required',
        ]);

        $event = new Event();
        $event->title = $request->title;
        $datetime = date("Y-m-d H:i:s", strtotime($request->start_date));
        $event->start_date = $datetime;
        $event->location = $request->location;

        // Associate status
        $type = EventType::find($request->type);
        $type->events()->save($event);

        return redirect('/events')->with(['status' => 'Event created. ', ]);
    }

    public function show($eventId): View
    {
        $event = Event::find($eventId);

        return view('events.show', compact('event' ));
    }

    public function edit($eventId): View
    {
        $event = Event::find($eventId);
        $types = EventType::all();

        return view('events.edit', compact('event',  'types'));
    }

    public function update($eventId, Request $request): RedirectResponse
    {
        $event = Event::find($eventId);
        $event->title = $request->title;
        $event->start_date = strtotime($request->start_date);
        $event->location = $request->location;


        // Associate status
        $type = EventType::find($request->type);
        $type->events()->save($event);

        return redirect()->route('event.edit', [$eventId]);
    }

    public function delete($eventId): RedirectResponse
    {
        $event = Event::find($eventId);

        $event->delete();

        return redirect()->route('events.index')->with(['status' => 'Event deleted. ', ]);
    }

    public function getFilters(Request $request): array
    {
        return [
            'type'    => $this->getFilterType($request),
        ];
    }
    public function getFilterType(Request $request): array
    {
        $default = 0;

        $types = EventType::all();
        $types_keyed = $types->mapWithKeys(function($item){
            return [ $item['id'] => $item['title'] ];
        });
        $types_keyed->prepend('Any type',0);

        return [
            'name'      => 'filter_type',
            'label'     => 'Type',
            'default'   => $default,
            'current'   => $request->input('filter_type', $default),
            'list'      => $types_keyed,
        ];
    }

    public function getSorts(Request $request): array
    {
        $sort_cols = [
            'title',
            'type.title',
            'created_at',
        ];

        // get URL ready
        $url = $request->url() . '?';
        $filters = $this->getFilters($request);
        foreach( $filters as $key => $filter ) {
            $url .= $filter['name'] . '=' . $filter['current'];
            if ( ! $key === array_key_last($filters) ) $url .= '&';
        }

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
