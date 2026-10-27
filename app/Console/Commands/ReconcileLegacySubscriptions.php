<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Billing\LegacySubscriptionSynchronizer;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Throwable;

class ReconcileLegacySubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:reconcile-legacy-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile Spark and free-tenant subscriptions';

    /**
     * Execute the console command.
     */
    public function handle(LegacySubscriptionSynchronizer $synchronizer): int
    {
        $failedTenantIds = collect();
        $reconciledTenantCount = 0;

        Tenant::query()->chunkById(100, function (Collection $tenants) use (
            $failedTenantIds,
            $synchronizer,
            &$reconciledTenantCount,
        ): void {
            $tenants->each(function (Tenant $tenant) use (
                $failedTenantIds,
                $synchronizer,
                &$reconciledTenantCount,
            ): void {
                try {
                    $synchronizer->sync($tenant);
                    $reconciledTenantCount++;
                } catch (Throwable $exception) {
                    report($exception);
                    $failedTenantIds->push($tenant->getKey());
                }
            });
        });

        if ($failedTenantIds->isNotEmpty()) {
            $this->error('Failed to reconcile tenants: '.$failedTenantIds->implode(', '));

            return self::FAILURE;
        }

        $this->info("Reconciled {$reconciledTenantCount} tenant subscriptions.");

        return self::SUCCESS;
    }
}
