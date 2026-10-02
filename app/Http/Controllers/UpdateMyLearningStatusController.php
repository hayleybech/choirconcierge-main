<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateMyLearningStatusController extends Controller
{
    public function __invoke(Song $song, Request $request): RedirectResponse
    {
        $allowedStatuses = ['not-started', 'assessment-ready'];

        if (auth()->user()->can('update', $song)) {
            $allowedStatuses[] = 'performance-ready';
        }

        $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
        ]);

        $song->createMissingLearningRecords();

        $song->members()->updateExistingPivot(auth()->user()->membership->id, ['status' => $request->input('status')]);

        return redirect()->route('songs.show', $song);
    }
}
