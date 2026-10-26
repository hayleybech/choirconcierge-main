<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
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
            ->allowedFilters(...[
                AllowedFilter::callback('type', fn (Builder $query, mixed $values) => MailLog::applyTypeFilter(
                    $query,
                    Arr::wrap($values),
                ))->default(MailLog::DEFAULT_TYPES),
                AllowedFilter::callback('read', function (Builder $query, mixed $value): void {
                    $email = strtolower(auth()->user()->email);
                    $openedByUser = fn (Builder $query) => $query->whereRaw('LOWER(context) = ?', [$email]);

                    if ($value === 'read') {
                        $query->whereHas('opens', $openedByUser);
                    } elseif ($value === 'unread') {
                        $query->whereDoesntHave('opens', $openedByUser);
                    }
                }),
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
            ->allowedSorts(...[
                'subject',
                'to',
                'from',
                'size',
                'created_at',
                ...($showAdminDetails ? [AllowedSort::field('opens', 'opens_count')] : []),
            ]);

        $logs = $query
            ->with('latestEvent.user_group')
            ->withCount([
                'opens as opened_by_current_user_count' => fn (Builder $query) => $query
                    ->whereRaw('LOWER(context) = ?', [strtolower(auth()->user()->email)]),
            ])
            ->when($showAdminDetails, fn ($query) => $query->withCount([
                'opens as opens_count' => fn (Builder $query) => $query->whereHas('mail_log', fn (Builder $query) => $query
                    ->where('uid', 'not like', 'product-announcement-%')
                ),
            ]))
            ->paginate(50)
            ->withQueryString();

        $logs->getCollection()->transform(fn (MailLog $log): MailLog => $log->setAttribute(
            'from_user',
            $this->findUserFromSender($log->from),
        )->setAttribute(
            'from_membership',
            tenancy()->initialized ? $log->from_user?->membership : null,
        )->setAttribute(
            'is_read',
            $log->opened_by_current_user_count > 0,
        ));

        $logs->getCollection()->each(function (MailLog $log): void {
            unset($log->opened_by_current_user_count);

            if ($log->isProductAnnouncement()) {
                $log->unsetRelation('latestEvent');
                unset($log->opens_count);
            }
        });

        return Inertia::render('MailingLists/MailLogs/Index', [
            'mailTypes' => collect(MailLog::TYPE_LABELS)
                ->map(fn (string $name, string $id): array => ['id' => $id, 'name' => $name])
                ->values(),
            'defaultMailTypes' => MailLog::DEFAULT_TYPES,
            'mailingLists' => UserGroup::query()
                ->orderBy('title')
                ->get(),
            'logs' => $logs,
        ]);
    }

    public function show(MailLog $mail_log): Response
    {
        $mail_log->events()->firstOrCreate([
            'status' => 'opened',
            'context' => auth()->user()->email,
        ]);

        $isProductAnnouncement = $mail_log->isProductAnnouncement();
        $showAdminDetails = $this->showAdminDetails() && ! $isProductAnnouncement;
        $log = $isProductAnnouncement
            ? $mail_log->setRelation('events', collect())
            : ($showAdminDetails
                ? $mail_log->load('events.user_group.tenant')->loadCount('opens')
                : $mail_log->load('events.user_group'));

        $log->setAttribute('from_user', $this->findUserFromSender($log->from));
        $log->setAttribute('from_membership', tenancy()->initialized ? $log->from_user?->membership : null);

        return Inertia::render('MailingLists/MailLogs/Show', [
            'log' => $log,
        ]);
    }

    public static function unreadCommunicationsCount(): int
    {
        $user = auth()->user();

        if ($user === null || ! tenancy()->initialized) {
            return 0;
        }

        $query = self::showAdminDetails()
            ? self::queryLogsAdmin()
            : self::queryLogsAuthorisedForUser();

        return MailLog::applyTypeFilter($query, MailLog::DEFAULT_TYPES)
            ->whereDoesntHave('opens', fn (Builder $query) => $query
                ->whereRaw('LOWER(context) = ?', [strtolower($user->email)])
            )
            ->count();
    }

    private static function showAdminDetails(): bool
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

    public static function queryLogsAdmin(): Builder
    {
        return MailLog::query()
            ->where(function (Builder $query): void {
                $query->whereHas('tenants', fn (Builder $query) => $query->where('tenants.id', tenant('id')))
                    ->orWhere('uid', 'like', 'product-announcement-%');
            });
    }

    public static function queryLogsAuthorisedForUser(): Builder
    {
        return MailLog::query()
            ->where(function (Builder $query): void {
                $query->whereHas('events', function (Builder $query): void {
                    $query->where('status', '=', 'group-found')
                        ->whereIn('user_group_id', self::getGroupsForUser());
                })->orWhere('uid', 'like', 'product-announcement-%');
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
