<?php

namespace App\Http\Controllers;

use App\Song;
use App\SongAttachmentCategory;
use App\SongCategory;
use App\SongStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;

class SongsController extends Controller
{
    public function index(Request $request): View
    {
        // Base query
        $songs = Song::with([]);

        // Filter songs
        $where = [];
        $filters = $this->getFilters($request);

        // Filter by status
        if($filters['status']['current'] !== 0) {
            $where[] = ['status_id', '=', $filters['status']['current']];
        }
        $songs = $songs->where($where);

        // Filter by category
        if($filters['category']['current'] !== 0) {
            $current = $filters['category']['current'];
            $songs = $songs->whereHas('categories', function($query) use($current) {
                $query->where('category_id', '=', $current);
            });
        }

        // Finish and fetch
        $songs = $songs->get();

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

        return view('songs', compact('songs', 'filters', 'sorts') );
    }

    public function create(): View
    {
        $categories = SongCategory::all();
        $statuses = SongStatus::all();
        $pitches = Song::getAllPitchesByMode();

        return view('songs.create', compact('categories', 'statuses', 'pitches') );
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'             => 'required|max:255',
            'categories'        => 'required|exists:song_categories,id',
            'status'            => 'required|exists:song_statuses,id',
            'pitch_blown'       => 'required',
        ]);

        $song = new Song();
        $song->title = $request->title;
        $song->pitch_blown = $request->pitch_blown;

        // Associate status
        $status = SongStatus::find($request->status);
        $status->songs()->save($song);

        // Attach categories
        $song->categories()->attach($request->categories);
        $song->save();

        return redirect('/songs')->with(['status' => 'Song created. ', ]);
    }

    public function show($songId): View
    {
        $song = Song::find($songId);
        $attachment_categories = SongAttachmentCategory::all();
        $categories_keyed = $attachment_categories->mapWithKeys(function($item){
            return [ $item['id'] => $item['title'] ];
        });

        return view('songs.show', compact('song', 'categories_keyed'));
    }

    public function edit($songId): View
    {
        $song = Song::find($songId);
        $categories = SongCategory::all();
        $statuses = SongStatus::all();
        $pitches = Song::getAllPitchesByMode();

        return view('songs.edit', compact('song', 'categories', 'statuses', 'pitches'));
    }

    public function update($songId, Request $request): RedirectResponse
    {
        $song = Song::find($songId);
        $song->title = $request->title;
        $song->pitch_blown = $request->pitch_blown;

        // Associate status
        $status = SongStatus::find($request->status);
        $status->songs()->save($song);

        // Attach categories
        $song->categories()->sync($request->categories);
        $song->save();

        return redirect()->route('song.edit', [$songId]);
    }

    public function delete($songId): RedirectResponse
    {
        $song = Song::find($songId);

        $song->delete();

        return redirect()->route('songs.index')->with(['status' => 'Song deleted. ', ]);
    }

    public function getFilters(Request $request): array
    {
        return [
            'status'    => $this->getFilterStatus($request),
            'category'    => $this->getFilterCategory($request),
        ];
    }
    public function getFilterStatus(Request $request): array
    {
        $default = 0;

        $statuses = SongStatus::all();
        $statuses_keyed = $statuses->mapWithKeys(function($item){
            return [ $item['id'] => $item['title'] ];
        });
        $statuses_keyed->prepend('All Songs',0);

        return [
            'name'      => 'filter_status',
            'label'     => 'Status',
            'default'   => $default,
            'current'   => $request->input('filter_status', $default),
            'list'      => $statuses_keyed,
        ];
    }

    public function getFilterCategory(Request $request): array
    {
        $default = 0;

        $categories = SongCategory::all();
        $categories_keyed = $categories->mapWithKeys(function($item){
            return [ $item['id'] => $item['title'] ];
        });
        $categories_keyed->prepend('All Categories',0);

        return [
            'name'      => 'filter_category',
            'label'     => 'Category',
            'default'   => $default,
            'current'   => $request->input('filter_category', $default),
            'list'      => $categories_keyed,
        ];
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
