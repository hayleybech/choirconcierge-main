<?php

namespace App\Http\Controllers;

use App\Song;
use App\SongCategory;
use App\SongStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class SongsController extends Controller
{
    public function index(){
        // Base query
        $songs = Song::with([]);

        // Filter songs
        $where = [];
        $filters = $this->getFilters();

        // Filter by status
        if($filters['status']['current'] != 0) {
            $where[] = ['status_id', '=', $filters['status']['current']];
        }
        $songs = $songs->where($where);

        // Finish and fetch
        $songs = $songs->get();


        return view('songs', compact('songs', 'filters') );
    }

    public function create() {
        $categories = SongCategory::all();
        $statuses = SongStatus::all();
        $pitches = Song::getAllPitchesByMode();

        return view('songs.create', compact('categories', 'statuses', 'pitches') );
    }

    public function store(Request $request) {
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

    public function edit() {
        return view('songs.edit');
    }

    public function getFilters() {
        return [
            'status'    => $this->getFilterStatus(),
        ];
    }
    public function getFilterStatus() {
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
            'current'   => Input::get('filter_status', $default),
            'list'      => $statuses_keyed,
        ];
    }
}
