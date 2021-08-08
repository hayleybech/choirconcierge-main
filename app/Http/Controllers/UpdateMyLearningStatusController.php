<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateMyLearningStatusController extends Controller
{
    public function __invoke(Song $song, Request $request): RedirectResponse
    {
        $song->createMissingLearningRecords();

        $song->singers()->updateExistingPivot(auth()->user()->singer->id, ['status' => 'assessment-ready']);

        return redirect()->route('songs.show', $song);
    }
}
