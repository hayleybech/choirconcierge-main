<?php

namespace App\Http\Controllers;

use App\Song;
use App\SongCategory;
use App\SongStatus;
use Illuminate\Http\Request;

class SongsController extends Controller
{
    public function index(){
        $songs = Song::all();
        return view('songs', compact('songs') );
    }

    public function create() {
        $categories = SongCategory::all();
        $statuses = SongStatus::all();

        return view('songs.create', compact('categories', 'statuses') );
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
}
