<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongRequest;
use App\Models\Song;
use App\Models\SongAttachmentCategory;
use App\Models\SongCategory;
use App\Models\SongStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SongController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Song::class);

        // Base query
        $songs = Song::withCount(['attachments'])
            ->filter()
            ->get();

        // Sort
        $sort_by = $request->input('sort_by', 'title');
        $sort_dir = $request->input('sort_dir', 'asc');

        // Flip direction for date (so we sort by smallest age not smallest timestamp)
        if($sort_by === 'created_at') $sort_dir = ($sort_dir === 'asc') ? 'desc' : 'asc';

        if( $sort_dir === 'asc') {
            $songs = $songs->sortBy($sort_by);
        } else {
            $songs = $songs->sortByDesc($sort_by);
        }

        return view('songs.index', [
            'all_songs'      => $songs,
            'active_songs'   => $songs->where('status.title', '=', 'Active'),
            'learning_songs' => $songs->where('status.title', '=', 'Learning'),
            'pending_songs'  => $songs->where('status.title', '=', 'Pending'),
            'archived_songs' => $songs->where('status.title', '=', 'Archived'),
            'filters'        => Song::getFilters(),
            'sorts'          => $this->getSorts($request),
        ]);
    }

    public function learning(Request $request): View
    {
        $this->authorize('viewAny', Song::class);

        // Base query
        $songs = Song::with(['attachments'])
            ->filter()
            ->get();

        // Sort
        $sort_by = $request->input('sort_by', 'name');
        $sort_dir = $request->input('sort_dir', 'asc');

        // Flip direction for date (so we sort by smallest age not smallest timestamp)
        if($sort_by === 'created_at') $sort_dir = ($sort_dir === 'asc') ? 'desc' : 'asc';

        if( $sort_dir === 'asc') {
            $songs = $songs->sortBy($sort_by);
        } else {
            $songs = $songs->sortByDesc($sort_by);
        }

        $sorts = $this->getSorts($request);

        $filters = Song::getFilters();
        return view('songs.learning', compact('songs', 'filters', 'sorts') );
    }

    public function create(): View
    {
        $this->authorize('create', Song::class);

        $categories = SongCategory::all();
        $statuses = SongStatus::all();
        $pitches = Song::KEYS;

        return view('songs.create', compact('categories', 'statuses', 'pitches') );
    }

    public function store(SongRequest $request): RedirectResponse
    {
        $this->authorize('create', Song::class);

        $song = Song::create(
            attributes: collect($request->validated())->except('send_notification')->toArray(),
            send_notification: $request->input('send_notification')
        );

        return redirect()->route('songs.show', [$song])->with(['status' => 'Song created. ']);
    }

    public function show(Song $song): View
    {
        $this->authorize('view', $song);

        $song->load('attachments.category');

        $attachment_categories = SongAttachmentCategory::all();
        $categories_keyed = $attachment_categories->mapWithKeys(function($item){
            return [ $item['id'] => $item['title'] ];
        });

        return view('songs.show', compact('song', 'categories_keyed'));
    }

    public function edit(Song $song): View
    {
        $this->authorize('update', $song);

        $categories = SongCategory::all();
        $statuses = SongStatus::all();
        $pitches = Song::KEYS;

        return view('songs.edit', compact('song', 'categories', 'statuses', 'pitches'));
    }

    public function update(SongRequest $request, Song $song): RedirectResponse
    {
        $this->authorize('update', $song);

        $song->update($request->validated());

        return redirect()->route('songs.show', [$song])->with(['status' => 'Song updated. ', ]);
    }

    public function destroy(Song $song): RedirectResponse
    {
        $this->authorize('delete', $song);

        $song->delete();

        return redirect()->route('songs.index')->with(['status' => 'Song deleted. ', ]);
    }

    public function getSorts(Request $request): array
    {
        $sort_cols = [
            'title',
            'status.title',
            'created_at',
        ];

        // Merge filters with sort query string
        $url = $request->url() . '?' . Song::getFilterQueryString();

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
