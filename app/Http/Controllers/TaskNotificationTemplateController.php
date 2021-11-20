<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationTemplateRequest;
use App\Models\NotificationTemplate;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\CommonMarkConverter;

class TaskNotificationTemplateController extends Controller
{
	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Task $task
	 *
	 * @return View
	 */
	public function create(Task $task): View
	{
		return view('tasks.notifications.create')->with(compact('task'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param NotificationTemplateRequest $request
	 * @param Task                        $task
	 *
	 * @return RedirectResponse
	 */
	public function store(NotificationTemplateRequest $request, Task $task): RedirectResponse
	{
		$template = $task->notification_templates()->create($request->validated());

		return redirect()
			->route('tasks.notifications.show', [$task, $template])
			->with(['status' => 'Notification created.']);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Task                 $task
	 * @param NotificationTemplate $notification
	 *
	 * @return View
	 */
	public function show(Task $task, NotificationTemplate $notification): View|Response
	{
        $task->can = [
            'update_task' => auth()->user()?->can('update', $task),
            'delete_task' => auth()->user()?->can('delete', $task),
        ];
        if(config('features.rebuild')){
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Tasks/Notifications/Show', [
                'task' => $task,
                'notification' => $notification->append('body_with_highlights'),
            ]);
        }

		return view('tasks.notifications.show')->with(compact('task', 'notification'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Task                 $task
	 * @param NotificationTemplate $notification
	 *
	 * @return View
	 */
	public function edit(Task $task, NotificationTemplate $notification): View|Response
	{
        if(config('features.rebuild')){
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Tasks/Notifications/Edit', [
                'task' => $task,
                'notification' => $notification,
            ]);
        }

		return view('tasks.notifications.edit')->with(compact('task', 'notification'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param NotificationTemplateRequest $request
	 * @param Task                        $task
	 * @param NotificationTemplate        $notification
	 *
	 * @return RedirectResponse
	 */
	public function update(
		NotificationTemplateRequest $request,
		Task $task,
		NotificationTemplate $notification
	): RedirectResponse {
		$notification->update($request->validated());
		return redirect()
			->route('tasks.notifications.show', [$task, $notification])
			->with(['status' => 'Task Notification update.']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Task                 $task
	 * @param NotificationTemplate $notification
	 *
	 * @return RedirectResponse
	 * @throws \Exception
	 */
	public function destroy(Task $task, NotificationTemplate $notification): RedirectResponse
	{
		$notification->delete();
		return redirect()
			->route('tasks.show', $task)
			->with(['status' => 'Task Notification deleted.']);
	}
}
