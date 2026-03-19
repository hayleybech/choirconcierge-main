<?php

namespace App\Console\Commands;

use App\Models\MailLog;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PopulateMailLogTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail-logs:populate-tenants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate tenant relationships for existing mail logs based on recipient domains.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting mail log tenant population...');

        $tenants = Tenant::all();
        $centralDomain = central_domain();

        $totalLogs = MailLog::count();
        $bar = $this->output->createProgressBar($totalLogs);

        MailLog::query()->chunkById(100, function ($logs) use ($tenants, $centralDomain, $bar) {
            foreach ($logs as $log) {
                $tenantIds = collect();

                collect(explode(',', $log->to))
                    ->merge(explode(',', (string) $log->cc))
                    ->merge(explode(',', (string) $log->bcc))
                    ->map(fn($email) => trim($email))
                    ->filter()
                    ->each(function ($recipient) use ($tenantIds, $tenants, $centralDomain) {
                        $domain = Str::of($recipient)->after('@');

                        $tenants->each(function ($tenant) use ($tenantIds, $domain, $centralDomain) {

                            // Subdomain
                            $tenantDomain = $tenant->primary_domain . '.' . $centralDomain;
                            if ((string) $domain === $tenantDomain) {
                                $tenantIds->push($tenant->id);
                                return;
                            }

                            // Domain
                            if ((string) $domain === $tenant->primary_domain) {
                                $tenantIds->push($tenant->id);
                                return;
                            }

                            if (Str::of((string) $domain)->explode('.')->first() === $tenant->primary_domain) {
                                $tenantIds->push($tenant->id);
                            }
                        });
                    });

                if ($tenantIds->isNotEmpty()) {
                    $log->tenants()->syncWithoutDetaching($tenantIds->unique());
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Mail log tenant population completed.');

        return \Illuminate\Console\Command::SUCCESS;
    }
}
