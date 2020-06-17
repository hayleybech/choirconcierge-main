<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Models\Singer;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SingerProfileController extends Controller
{
    const PROFILE_TASK_ID = 1;

    public function create(Singer $singer): View
    {
        return view('singers.createprofile', compact('singer'));
    }

    public function store(Singer $singer, Request $request): RedirectResponse
    {
        $singer->profile()->create($request->all()); // refer to whitelist in model

        if( $singer->onboarding_enabled ) {
            // Mark matching task completed
            //$task = $singer->tasks()->where('name', 'Member Profile')->get();
            $singer->tasks()->updateExistingPivot( self::PROFILE_TASK_ID, array('completed' => true) );

            event( new TaskCompleted(Task::find(self::PROFILE_TASK_ID), $singer) );
        }

        return redirect()->route('singers.show', $singer)->with(['status' => 'Member Profile created. ', ]);
    }
}
