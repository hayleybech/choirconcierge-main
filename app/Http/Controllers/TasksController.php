<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use Illuminate\View\View;

class TasksController extends Controller
{
    public function index(): View
    {
        $tasks = Task::all();

        return view('tasks.index', compact('tasks'));
    }
}
