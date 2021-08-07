<?php

namespace App\Http\Controllers;

use App\Models\Singer;
use App\Models\Song;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LearningStatusController extends Controller
{
    public function index(Song $song): View
    {
        $this->authorize('update', $song);

        $song->createMissingLearningRecords();

        $song->load('singers');

        return view('songs.learning.index', [
            'song' => $song,
            'singers' => $song->singers()->with(['user', 'voice_part'])->get(),
        ]);
    }

    public function update(Song $song, Singer $singer, Request $request)
    {
        $this->authorize('update', $song);

        $song->singers()->updateExistingPivot($singer->id, ['status' => $request->input('status')]);

        return redirect()->route('songs.singers.index', $song);
    }
}
