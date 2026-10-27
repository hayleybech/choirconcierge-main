<?php

namespace App\Services;

use App\Enums\SingerStatus;
use App\Models\Membership;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TenantStats
{
    /**
     * @return array{activeTenants: int, tenantsOnTrial: int, tenantsTrialExpired: int, activeMembers: int, trialConversionRate: float|int, medianRetentionTime: float|int|null}|null
     */
    public function get(): ?array
    {
        if (! auth()->user()->isSuperAdmin) {
            return null;
        }

        return [
            'activeTenants' => Tenant::active()->count(),
            'tenantsOnTrial' => Tenant::trial()->count(),
            'tenantsTrialExpired' => Tenant::expiredTrial()->count(),
            'activeMembers' => $this->getActiveMembersCount(),
            'trialConversionRate' => $this->getTrialConversionRate(),
            'medianRetentionTime' => $this->getMedianRetentionTime(),
        ];
    }

    private function getTrialConversionRate(): float|int
    {
        $totalTrialed = Tenant::whereHas('customer', function (Builder $query): void {
            $query->whereNotNull('trial_ends_at');
        })->orWhereHas('subscriptions', function (Builder $query): void {
            $query->whereNotNull('trial_ends_at');
        })->count();

        if ($totalTrialed === 0) {
            return 0;
        }

        $converted = Tenant::whereHas('subscriptions', function (Builder $query): void {
            $query->where(function (Builder $query): void {
                $query->whereNull('trial_ends_at')
                    ->orWhere('trial_ends_at', '<', Carbon::now());
            })->where('paddle_status', 'active');
        })->count();

        return round(($converted / $totalTrialed) * 100, 2);
    }

    private function getMedianRetentionTime(): float|int|null
    {
        $medianRetentionTime = Tenant::whereHas('subscriptions', function (Builder $query): void {
            $query->where('paddle_status', 'active')
                ->orWhereNotNull('ends_at');
        })->with('subscriptions')->get()->map(function (Tenant $tenant): float|int {
            $firstSub = $tenant->subscriptions->sortBy('created_at')->first();
            $lastSub = $tenant->subscriptions->sortByDesc('ends_at')->first();

            $start = Carbon::parse($firstSub->created_at);
            $end = $lastSub->ends_at ? Carbon::parse($lastSub->ends_at) : Carbon::now();

            return $start->diffInMonths($end, false);
        })->sort()
            ->median();

        return $medianRetentionTime === null ? null : round($medianRetentionTime, 2);
    }

    private function getActiveMembersCount(): int
    {
        return Membership::whereHas('status', function (Builder $query): void {
            $query->where('status', SingerStatus::MEMBERS->value);
        })->whereHas('tenant', function (Builder $query): void {
            $query->active();
        })->count();
    }
}
