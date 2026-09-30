<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use App\Models\UserGroup;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class MailLogController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MailLog::class);
    }

    public function index(): Response
    {
        $showAdminDetails = $this->showAdminDetails();

        return Inertia::render('MailingLists/MailLogs/Index', [
            'logs' => (
                $showAdminDetails
            )
                ? self::queryLogsAdmin()
                    ->latest()
                    ->with('latestEvent.user_group')
                    ->withCount('opens')
                    ->paginate(50)
                : self::queryLogsAuthorisedForUser()->with('latestEvent.user_group')->latest()->paginate(50),
        ]);
    }

    public function show(MailLog $mail_log): Response
    {
        $showAdminDetails = $this->showAdminDetails();

        return Inertia::render('MailingLists/MailLogs/Show', [
            'log' => $showAdminDetails
                ? $mail_log->load('events.user_group.tenant')->loadCount('opens')
                : $mail_log->load('events.user_group'),
        ]);
    }

    private function showAdminDetails(): bool
    {
        return auth()->user()->can('createBroadcast', UserGroup::class)
            || auth()->user()->can('update', [UserGroup::class, null]);
    }

    public static function queryLogsAdmin()
    {
        return MailLog::query()
            ->whereHas('tenants', function ($query) {
                $query->where('tenants.id', tenant('id'));
            });
    }

    public static function queryLogsAuthorisedForUser()
    {
        return MailLog::query()
            ->whereHas('events', function ($query) {
                $query->where('status', '=', 'group-found')
                    ->whereIn('user_group_id', self::getGroupsForUser());
            });
    }

    public static function getGroupsForUser(): Collection
    {
        return UserGroup::with([
            'tenant',
            'recipient_roles',
            'recipient_ensembles',
            'sender_roles',
            'sender_ensembles',
        ])
            ->get()
            ->filter(fn(UserGroup $group) => $group->get_all_recipients()->contains(auth()->user())
                || $group->get_all_senders()->contains(auth()->user()))
            ->map(fn(UserGroup $group) => $group->id);
    }
}
