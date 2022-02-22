<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateMyLearningStatusController extends Controller
{
    public function __invoke(Song $song, Request $request): RedirectResponse
    {
        $request->validate([
            'status' => ['in:not-started,assessment-ready'],
        ]);

        $song->createMissingLearningRecords();

        $song->singers()->updateExistingPivot(auth()->user()->singer->id, ['status' => $request->input('status')]);

        return redirect()->route('songs.show', $song);
    }
}
