<?php

namespace App\Console\Commands;

use App\Models\MailLog;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
            $pivotData = [];

            // Pre-calculate tenant domains to avoid string concatenation in loop
            $tenantLookup = $tenants->map(function ($tenant) use ($centralDomain) {
                return [
                    'id' => $tenant->id,
                    'primary_domain' => (string) $tenant->primary_domain,
                    'full_domain' => $tenant->primary_domain . '.' . $centralDomain,
                ];
            });

            foreach ($logs as $log) {
                $tenantIds = [];

                $recipients = explode(',', $log->to . ',' . $log->cc . ',' . $log->bcc);

                foreach ($recipients as $recipient) {
                    $recipient = trim($recipient);
                    if (empty($recipient)) {
                        continue;
                    }

                    $domain = substr(strrchr($recipient, "@"), 1);
                    if (!$domain) {
                        continue;
                    }

                    foreach ($tenantLookup as $t) {
                        if ($domain === $t['full_domain'] || $domain === $t['primary_domain']) {
                            $tenantIds[] = $t['id'];
                            continue;
                        }

                        if (strpos($domain, '.') !== false && explode('.', $domain)[0] === $t['primary_domain']) {
                            $tenantIds[] = $t['id'];
                        }
                    }
                }

                foreach (array_unique($tenantIds) as $tenantId) {
                    $pivotData[] = [
                        'mail_log_id' => $log->id,
                        'tenant_id' => $tenantId,
                    ];
                }

                $bar->advance();
            }

            if (! empty($pivotData)) {
                DB::table('mail_log_tenant')->insertOrIgnore($pivotData);
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Mail log tenant population completed.');

        return \Illuminate\Console\Command::SUCCESS;
    }
}
