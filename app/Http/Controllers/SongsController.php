<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongAttachmentCategory;
use App\Models\SongCategory;
use App\Models\SongStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;

class SongsController extends Controller
{
    public function index(Request $request): View
    {
        // Base query
        $songs = Song::with([])
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
        return view('songs.index', compact('songs', 'filters', 'sorts') );
    }

    public function learning(Request $request): View
    {
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
        $categories = SongCategory::all();
        $statuses = SongStatus::all();
        $pitches = Song::KEYS;

        return view('songs.create', compact('categories', 'statuses', 'pitches') );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRequest();
        $song = Song::create($data);

        // Associate status
        $status = SongStatus::find($request->status);
        $status->songs()->save($song);

        // Attach categories
        $song->categories()->attach($request->categories);
        $song->save();

        return redirect('/songs')->with(['status' => 'Song created. ', ]);
    }

    public function show(Song $song): View
    {
        $attachment_categories = SongAttachmentCategory::all();
        $categories_keyed = $attachment_categories->mapWithKeys(function($item){
            return [ $item['id'] => $item['title'] ];
        });

        return view('songs.show', compact('song', 'categories_keyed'));
    }

    public function edit(Song $song): View
    {
        $categories = SongCategory::all();
        $statuses = SongStatus::all();
        $pitches = Song::KEYS;

        return view('songs.edit', compact('song', 'categories', 'statuses', 'pitches'));
    }

    public function update(Song $song, Request $request): RedirectResponse
    {
        $data = $this->validateRequest($song);
        $song->update($data);

        // Associate status
        $status = SongStatus::find($request->status);
        $status->songs()->save($song);

        // Attach categories
        $song->categories()->sync($request->categories);
        $song->save();

        return redirect()->route('song.edit', [$song])->with(['status' => 'Song updated. ', ]);
    }

    public function delete(Song $song): RedirectResponse
    {
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

        // get URL ready
        $url = $request->url() . '?';
        $filters = Song::getFilters();
        foreach( $filters as $key => $filter ) {
            $url .= $filter->name . '=' . $filter->current_option;

            if ( $key !== array_key_last($filters) ) {
                $url .= '&';
            }
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

    /**
     * @param Song $song
     * @return mixed
     */
    public function validateRequest(Song $song = null)
    {
        return request()->validate([
            'title'             => 'required|max:255',
            'categories'        => 'required|exists:song_categories,id',
            'status'            => 'required|exists:song_statuses,id',
            'pitch_blown'       => 'required',
        ]);
    }
}
