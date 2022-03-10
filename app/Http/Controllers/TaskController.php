<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Role;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): View|Response
    {
        return Inertia::render('Tasks/Index', [
            'tasks' => Task::all()->values(),
        ]);
    }

    public function create(): View|Response
    {
        return Inertia::render('Tasks/Create', [
            'roles' => Role::where('name', '!=', 'User')->get()->values(),
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $data = array_merge($request->validated(), [
            'type' => 'manual',
            'route' => 'task.complete',
        ]);
        $task = Task::create($data);

        return redirect()
            ->route('tasks.show', $task)
            ->with(['status' => 'Task created.']);
    }

    public function show(Task $task): View|Response
    {
        $task->load('notification_templates');

        $task->can = [
            'delete_task' => auth()->user()?->can('delete', $task),
        ];

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with(['status' => 'Task deleted.']);
    }
}
