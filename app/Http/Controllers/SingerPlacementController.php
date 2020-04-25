<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Models\Singer;
use App\Models\Task;
use App\Models\VoicePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SingerPlacementController extends Controller
{
    const PLACEMENT_TASK_ID = 2;

    public function create(Singer $singer): View
    {
        $voice_parts = VoicePart::all()->pluck('title', 'id');

        return view('singers.createplacement', compact('singer', 'voice_parts'));
    }

    public function store(Singer $singer, Request $request): RedirectResponse
    {
        $singer->placement()->create($request->all()); // refer to whitelist in model

        if( $singer->onboarding_enabled ) {
            // Mark matching task completed
            //$task = $singer->tasks()->where('name', 'Voice Placement')->get();
            $singer->tasks()->updateExistingPivot( self::PLACEMENT_TASK_ID, array('completed' => true) );

            event( new TaskCompleted(Task::find(self::PLACEMENT_TASK_ID), $singer) );
        }

        return redirect('/singers')->with(['status' => 'Voice Placement created. ', ]);
    }
}
