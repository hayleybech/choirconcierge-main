<?php

namespace App\Http\Controllers;

use App\Models\Singer;
use App\Models\Song;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LearningStatusController extends Controller
{
    public function index(Song $song): View
    {
        $this->authorize('update', $song);

        $song->createMissingLearningRecords();

        $song->load('singers');

        $voice_parts = $song->singers()->with(['user', 'voice_part'])->get()
            ->sortBy('user.first_name')
            ->groupBy('voice_part.id')
            ->map(function($singers) {
                $part = $singers->first()->voice_part ?? VoicePart::getNullVoicePart();
                $part->singers = $singers;
                return $part;
            });
        $voice_parts = $this->moveNoPartToEnd($voice_parts);

        return view('songs.learning.index', [
            'song' => $song,
            'voice_parts' => $voice_parts,
        ]);
    }

    public function update(Song $song, Singer $singer, Request $request)
    {
        $this->authorize('update', $song);

        $song->singers()->updateExistingPivot($singer->id, ['status' => $request->input('status')]);

        return redirect()->route('songs.singers.index', $song);
    }

    private function moveNoPartToEnd($collection){
        return $collection->reject(function($value){
            return $value->title === 'No Part';
        })
        ->merge($collection->filter(function($value){
                return $value->title === 'No Part';
            })
        );
    }
}
