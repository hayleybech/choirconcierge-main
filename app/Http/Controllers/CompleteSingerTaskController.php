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
        $this->authorize('update', $singer); // @todo Check user has the role needed to complete the task

        event(new TaskCompleted($task, $singer));

        if ($task->type !== 'manual') {
            // Shouldn't get to this line. Forms tasks skip this entire function.
            throw new \LogicException('This function should only be called for manual tasks.');
        }

        // Simply mark as done.
        $singer->tasks()->updateExistingPivot($task, ['completed' => true]);

        return redirect()->route('singers.index')->with(['status' => 'Task updated. ']);
    }
}
