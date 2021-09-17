<?php

namespace App\Http\Controllers;

use App\Models\Singer;
use App\Models\Song;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class LearningStatusController extends Controller
{
    public function index(Song $song): View|InertiaResponse
    {
        $this->authorize('update', $song);

        $song->createMissingLearningRecords();

        $song->load('singers.user', 'singers.voice_part');

        $voice_parts = VoicePart::all()
            ->push(VoicePart::getNullVoicePart())
            ->map(function($part) use ($song) {
                $part->singers = $song->singers->where('voice_part_id', $part->id)->values();
                return $part;
            });

        if(config('features.rebuild')) {
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Songs/Learning/Index', [
                'song' => $song,
                'voice_parts' => $voice_parts->values(),
            ]);
        }

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
