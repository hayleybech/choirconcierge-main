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
        return Inertia::render('MailingLists/MailLogs/Index', [
            'logs' => (
                auth()->user()->isSuperAdmin() ||
                auth()->user()?->membership?->hasRole('Admin')
            )
                ? self::queryLogsAdmin()
                    ->latest()
                    ->with('latestEvent')
                    ->withCount('opens')
                    ->paginate(50)
                : self::queryLogsAuthorisedForUser()
                    ->latest()
                    ->with('latestEvent')
                    ->withCount('opens')
                    ->paginate(50)
        ]);
    }

    public function show(MailLog $mail_log): Response
    {
        return Inertia::render('MailingLists/MailLogs/Show', [
            'log' => $mail_log->load('events.user_group')->loadCount('opens'),
        ]);
    }

    public static function queryLogsAdmin()
    {
        return MailLog::query()
            ->where('to', 'like', '%@' . tenant('primary_domain') . '%')
            ->orWhere('cc', 'like', '%@' . tenant('primary_domain') . '%')
            ->orWhere('bcc', 'like', '%@' . tenant('primary_domain') . '%');
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
        return UserGroup::with('members.memberable', 'senders.sender')
            ->get()
            ->filter(fn(UserGroup $group) => $group->get_all_recipients()->contains(auth()->user())
                || $group->get_all_senders()->contains(auth()->user())
            )
            ->map(fn(UserGroup $group) => $group->id);
    }
}
