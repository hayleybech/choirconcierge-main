<?php

use App\Models\MailLog;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tenants = Tenant::all();
        $centralDomain = central_domain();

        MailLog::chunk(100, function ($logs) use ($tenants, $centralDomain) {
            foreach ($logs as $log) {
                $tenantIds = collect();

                collect(explode(',', $log->to))
                    ->merge(explode(',', $log->cc))
                    ->merge(explode(',', $log->bcc))
                    ->map(fn($email) => trim($email))
                    ->filter()
                    ->each(function ($recipient) use ($tenantIds, $tenants, $centralDomain) {
                        $domain = Str::of($recipient)->after('@');

                        $tenants->each(function ($tenant) use ($tenantIds, $domain, $centralDomain) {
                            if($domain === $tenant->primary_domain) {
                                $tenantIds->push($tenant->id);
                                return;
                            }
                            if(Str::of($domain)->explode('.')->first() === $tenant->primary_domain) {
                                $tenantIds->push($tenant->id);
                            }
                        });
                    });

                if ($tenantIds->isNotEmpty()) {
                    $log->tenants()->syncWithoutDetaching($tenantIds->unique());
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('mail_log_tenant')->truncate();
    }
};
