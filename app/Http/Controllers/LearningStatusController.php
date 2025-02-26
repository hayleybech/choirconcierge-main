<?php

namespace App\Http\Controllers;

use App\Models\Membership;
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

        $song->load(['members' => function ($query) {
            $query->active()->with('user', 'enrolments.voice_part');
        }]);

        $voice_parts = VoicePart::all()
            ->push(VoicePart::getNullVoicePart())
            ->map(function ($part) use ($song) {
                $part->members = $song->members
                    ->filter(fn($member) => $member
                        ->enrolments
                        ->contains(fn($enrolment) => $enrolment->voice_part_id === $part->id)
                    )
                    ->values();

                return $part;
            });

        return Inertia::render('Songs/Learning/Index', [
            'song' => $song,
            'voiceParts' => $voice_parts->values(),
        ]);
    }

    public function update(Song $song, Membership $singer, Request $request)
    {
        $this->authorize('update', $song);

        $song->members()->updateExistingPivot($singer->id, ['status' => $request->input('status')]);

        return redirect()->route('songs.singers.index', $song);
    }
}
