<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MailLogController extends Controller
{
    public function index(): Response
    {
        $logs = QueryBuilder::for(MailLog::class)
            ->allowedFilters([
                AllowedFilter::callback('type', fn (Builder $query, mixed $values) => MailLog::applyTypeFilter(
                    $query,
                    Arr::wrap($values),
                ))->default(MailLog::DEFAULT_TYPES),
                AllowedFilter::callback('read', function (Builder $query, mixed $value): void {
                    if ($value === 'read') {
                        $query->whereHas('opens');
                    } elseif ($value === 'unread') {
                        $query->whereDoesntHave('opens');
                    }
                }),
                'subject',
                'from',
                'to',
                AllowedFilter::callback('tenant', fn (Builder $query, $value) => $query
                    ->whereHas('tenants', fn (Builder $query) => $query->where('tenants.id', $value))
                ),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                'subject',
                'from',
                'to',
                'size',
                'created_at',
                AllowedSort::field('opens', 'opens_count'),
                AllowedSort::callback('tenant', function (Builder $query, bool $descending): void {
                    $query->orderBy(
                        Tenant::query()
                            ->from('tenants as sort_tenants')
                            ->select('sort_tenants.id')
                            ->join('mail_log_tenant as sort_mail_log_tenant', 'sort_mail_log_tenant.tenant_id', '=', 'sort_tenants.id')
                            ->whereColumn('sort_mail_log_tenant.mail_log_id', 'mail_logs.id')
                            ->orderBy('sort_tenants.id')
                            ->limit(1),
                        $descending ? 'desc' : 'asc',
                    );
                }),
            ])
            ->with(['latestEvent.user_group.tenant', 'tenants'])
            ->withCount('opens')
            ->paginate(50)
            ->withQueryString();

        $logs->getCollection()->transform(fn (MailLog $log): MailLog => $log->setAttribute(
            'from_user',
            $this->findUserFromSender($log->from),
        )->setAttribute(
            'is_read',
            $log->opens_count > 0,
        ));

        return Inertia::render('Central/MailLogs/Index', [
            'mailTypes' => collect(MailLog::TYPE_LABELS)
                ->map(fn (string $name, string $id): array => ['id' => $id, 'name' => $name])
                ->values(),
            'defaultMailTypes' => MailLog::DEFAULT_TYPES,
            'tenants' => Tenant::query()->orderBy('id')->get(),
            'logs' => $logs,
        ]);
    }

    public function show(MailLog $mail_log): Response
    {
        return Inertia::render('Central/MailLogs/Show', [
            'log' => $mail_log->load(['events.user_group.tenant', 'tenants'])->loadCount('opens'),
        ]);
    }

    private function findUserFromSender(string $sender): ?User
    {
        preg_match('/<([^>]+)>/', $sender, $matches);
        $email = $matches[1] ?? trim($sender);

        return User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();
    }
}
