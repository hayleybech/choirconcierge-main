<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Models\Singer;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompleteSingerTaskController extends Controller
{
    public function __invoke(Singer $singer, Task $task): RedirectResponse
    {
        event(new TaskCompleted($task, $singer));

        // Complete type-specific action
        if( $task->type === 'manual' ) {
            // Simply mark as done.
            $singer->tasks()->updateExistingPivot($task, ['completed' => true]);
            return redirect('/singers')->with(['status' => 'Task updated. ', ]);
        } else {
            // Redirect to form
            // Shouldn't get to this line. Forms tasks skip this entire function.
        }
    }
}
