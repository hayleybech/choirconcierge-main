<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateMyLearningStatusController extends Controller
{
    public function __invoke(Song $song, Request $request): RedirectResponse
    {
        if($song->my_learning->status === 'not-started'){
            $song->singers()->attach(auth()->user()->singer->id, ['status' => 'assessment-ready']);
        } else {
            $song->singers()->updateExistingPivot(auth()->user()->singer->id, ['status' => 'assessment-ready']);
        }

        return redirect()->route('songs.show', $song);
    }
}
