<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::all();

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $roles_keyed = $roles->mapWithKeys(static function($role){
            return [ $role['id'] => $role['name'] ];
        });
        return view('tasks.create')->with(compact('roles_keyed'));
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $data = array_merge($request->validated(), [
            'type'  => 'manual',
            'route' => 'task.complete',
        ]);
        $task = Task::create($data);
        return redirect()->route('tasks.show', $task)->with(['status' => 'Task created.']);
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
