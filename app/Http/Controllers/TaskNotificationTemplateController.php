<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationTemplateRequest;
use App\Models\NotificationTemplate;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskNotificationTemplateController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    public function create(Task $task): Response
    {
        return Inertia::render('Tasks/Notifications/Create', [
            'task' => $task,
        ]);
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

    public function show(Task $task, NotificationTemplate $notification): Response
    {
        $task->can = [
            'update_task' => auth()->user()?->can('update', $task),
            'delete_task' => auth()->user()?->can('delete', $task),
        ];

        return Inertia::render('Tasks/Notifications/Show', [
            'task' => $task,
            'notification' => $notification->append('body_with_highlights'),
        ]);
    }

    public function edit(Task $task, NotificationTemplate $notification): Response
    {
        return Inertia::render('Tasks/Notifications/Edit', [
            'task' => $task,
            'notification' => $notification,
        ]);
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
