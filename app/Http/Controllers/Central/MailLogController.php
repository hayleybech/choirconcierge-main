<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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
                'created_at',
                AllowedSort::callback('tenant', function (Builder $query, bool $descending): void {
                    $query->orderBy(
                        DB::table('mail_log_tenant')
                            ->join('tenants', 'tenants.id', '=', 'mail_log_tenant.tenant_id')
                            ->whereColumn('mail_log_tenant.mail_log_id', 'mail_logs.id')
                            ->orderBy('tenants.id')
                            ->select('tenants.id')
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
        ));

        return Inertia::render('Central/MailLogs/Index', [
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
