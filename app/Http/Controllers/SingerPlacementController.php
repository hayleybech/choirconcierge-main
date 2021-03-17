<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\PlacementRequest;
use App\Models\Placement;
use App\Models\Singer;
use App\Models\Task;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SingerPlacementController extends Controller
{
    const PLACEMENT_TASK_ID = 2;

    public function __construct()
    {
        $this->authorizeResource(Placement::class, 'placement');
    }

    public function create(Singer $singer): View
    {
        $voice_parts = [0 => "None"] + VoicePart::all()->pluck('title', 'id')->toArray();

        return view('singers.createplacement', compact('singer', 'voice_parts'));
    }

    public function store(Singer $singer, PlacementRequest $request): RedirectResponse
    {
        $singer->placement()->create($request->validated()); // refer to whitelist in model

        if( $singer->onboarding_enabled ) {
            // Mark matching task completed
            //$task = $singer->tasks()->where('name', 'Voice Placement')->get();
            $singer->tasks()->updateExistingPivot( self::PLACEMENT_TASK_ID, ['completed' => true] );

            event( new TaskCompleted(Task::find(self::PLACEMENT_TASK_ID), $singer) );
        }

        $singer->update([
            'voice_part_id' => $request->validated()['voice_part_id']
        ]);

        return redirect()->route('singers.show', $singer)->with(['status' => 'Voice Placement created. ', ]);
    }

    public function edit(Singer $singer, Placement $placement, Request $request): View
    {
        $voice_parts = [0 => "None"] + VoicePart::all()->pluck('title', 'id')->toArray();
        return view('singers.editplacement', compact('singer', 'placement', 'voice_parts'));
    }

    public function update(PlacementRequest $request, Singer $singer, Placement $placement): RedirectResponse
    {
        $placement->update($request->validated());

        $singer->update([
            'voice_part_id' => $request->validated()['voice_part_id']
        ]);

        return redirect()->route('singers.show', $singer)->with(['status' => 'Voice Placement updated.']);
    }
}
