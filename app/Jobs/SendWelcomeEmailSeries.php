<?php

namespace App\Jobs;

use App\Jobs\SendTenantWelcomePart1;
use App\Jobs\SendTenantWelcomePart2;
use App\Jobs\SendTenantWelcomePart3;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailSeries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Tenant $tenant)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    if(!$this->tenant->created_by) {
		    return; // This email type doesn't need to be sent to tenants created by super-admin.
	    }

	    $ownerUser = User::findOrFail($this->tenant->created_by);

		// Part 1
        SendTenantWelcomePart1::dispatch($ownerUser)
            ->onConnection('database')
            ->onQueue('delayed');

		// Part 2
        SendTenantWelcomePart2::dispatch($ownerUser, $this->tenant->had_demo ?? false)
            ->delay(now()->addDays(7))
            ->onConnection('database')
            ->onQueue('delayed');

	    // Part 3
        SendTenantWelcomePart3::dispatch($ownerUser, $this->tenant->had_demo ?? false)
            ->delay(now()->addDays(25))
            ->onConnection('database')
            ->onQueue('delayed');
    }
}
