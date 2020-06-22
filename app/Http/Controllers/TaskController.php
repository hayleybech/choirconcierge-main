<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::all();

        return view('tasks.index', compact('tasks'));
    }

    public function show(Task $task): View
    {
        $task->load('notification_templates');
        return view('tasks.show', compact('task'));
    }

    public function destroy(Task $task): RedirectResponse {
        $task->delete();

        return redirect()->route('tasks.index')->with(['status' => 'Task deleted.']);
    }
}
