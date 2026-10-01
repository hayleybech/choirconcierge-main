<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MailLogController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MailLog::class);
    }

    public function index(): Response
    {
        $showAdminDetails = $this->showAdminDetails();
        $query = QueryBuilder::for($showAdminDetails ? self::queryLogsAdmin() : self::queryLogsAuthorisedForUser())
            ->allowedFilters([
                'subject',
                AllowedFilter::callback('to', function ($query, $values): void {
                    $query->where(function ($query) use ($values): void {
                        foreach (Arr::wrap($values) as $value) {
                            $query->orWhere('to', 'like', "%{$value}%");
                        }
                    });
                }),
                'from',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['subject', 'to', 'from', 'created_at']);

        $logs = $query
            ->with('latestEvent.user_group')
            ->when($showAdminDetails, fn ($query) => $query->withCount('opens'))
            ->paginate(50)
            ->withQueryString();

        $logs->getCollection()->transform(fn (MailLog $log): MailLog => $log->setAttribute(
            'from_user',
            $this->findUserFromSender($log->from),
        )->setAttribute(
            'from_membership',
            tenancy()->initialized ? $log->from_user?->membership : null,
        ));

        return Inertia::render('MailingLists/MailLogs/Index', [
            'mailingLists' => UserGroup::query()
                ->orderBy('title')
                ->get(),
            'logs' => $logs,
        ]);
    }

    public function show(MailLog $mail_log): Response
    {
        $showAdminDetails = $this->showAdminDetails();

        $log = $showAdminDetails
                ? $mail_log->load('events.user_group.tenant')->loadCount('opens')
                : $mail_log->load('events.user_group');
        $log->setAttribute('from_user', $this->findUserFromSender($log->from));
        $log->setAttribute('from_membership', tenancy()->initialized ? $log->from_user?->membership : null);

        return Inertia::render('MailingLists/MailLogs/Show', [
            'log' => $log,
        ]);
    }

    private function showAdminDetails(): bool
    {
        return auth()->user()->can('createBroadcast', UserGroup::class)
            || auth()->user()->can('update', [UserGroup::class, null]);
    }

    private function findUserFromSender(string $sender): ?User
    {
        preg_match('/<([^>]+)>/', $sender, $matches);
        $email = $matches[1] ?? trim($sender);

        return User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();
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
