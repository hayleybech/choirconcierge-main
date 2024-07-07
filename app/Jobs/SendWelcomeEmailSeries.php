<?php

namespace App\Jobs;

use App\Mail\TenantWelcomePart1;
use App\Mail\TenantWelcomePart2;
use App\Mail\TenantWelcomePart3;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
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
	    Mail::to($ownerUser)
		    ->send(new TenantWelcomePart1($ownerUser));

		// Part 2
	    Mail::to($ownerUser)
		    ->later(now()->addDays(7), new TenantWelcomePart2($ownerUser, $this->tenant->had_demo ?? false));

	    // Part 3
	    Mail::to($ownerUser)
		    ->later(now()->addDays(25), new TenantWelcomePart3($ownerUser, $this->tenant->had_demo ?? false));
    }
}
