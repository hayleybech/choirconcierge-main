<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Contracts\View\View;

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
}
