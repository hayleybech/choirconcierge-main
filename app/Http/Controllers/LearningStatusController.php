<?php

namespace App\Http\Controllers;

use App\Models\LearningStatus;
use App\Models\Singer;
use App\Models\Song;
use Illuminate\Contracts\View\View;

class LearningStatusController extends Controller
{
    public function index(Song $song): View
    {
        $this->authorize('update', $song);

        $song->load('singers');

        $singers_with = $song->singers()->with(['user', 'voice_part'])->get();
        $singers_without = Singer::with(['user', 'voice_part'])->whereDoesntHave('songs', function($query) use ($song) {
            $query->where('song_id', '=', $song->id);
        })
        ->get()
        ->each(function(Singer $singer) {
            $singer->learning = LearningStatus::getNullLearningStatus();
        });
        $singers = $singers_with->merge($singers_without);

        return view('songs.learning.index', [
            'song' => $song,
            'singers' => $singers,
        ]);
    }
}
