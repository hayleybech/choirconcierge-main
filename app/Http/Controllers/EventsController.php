<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventsController extends Controller
{
    public function index(Request $request): View
    {
        // Base query
        $events = Event::with([])
            ->filter()
            ->get();

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
        return view('events.index', compact('events', 'filters', 'sorts') );
    }

    public function create(): View
    {
        $types = EventType::all();

        return view('events.create', compact( 'types') );
    }

    public function store(): RedirectResponse
    {
        $data = $this->validateRequest();
        $event = Event::create($data);

        // Associate status
        $type = EventType::find($data['type']);
        $type->events()->save($event);

        return redirect('/events')->with(['status' => 'Event created. ', ]);
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

    public function update(Event $event): RedirectResponse
    {
        $data = $this->validateRequest();
        $event->update($data);

        // Associate status
        $type = EventType::find($data['type']);
        $type->events()->save($event);

        return redirect()->route('event.edit', [$event])->with(['status' => 'Event updated. ', ]);
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

    /**
     * @return mixed
     */
    public function validateRequest()
    {
        return request()->validate([
            'title'             => 'required|max:255',
            'type'              => 'required|exists:event_types,id',
            'call_time'         => 'required|date_format:Y-m-d H:i:s|before:start_date',
            'start_date'        => 'required|date_format:Y-m-d H:i:s',
            'end_date'          => 'required|date_format:Y-m-d H:i:s|after:start_date',
            'location_place_id' => 'nullable',
            'location_icon'     => 'nullable',
            'location_name'     => 'nullable',
            'location_address'  => 'nullable',
            'description'       => 'nullable',
        ]);
    }
}
