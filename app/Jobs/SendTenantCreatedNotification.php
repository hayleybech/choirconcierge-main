<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TenantCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Notification;

class SendTenantCreatedNotification implements ShouldQueue
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
    public function handle(): void
    {
	    Notification::send(
		    User::firstOrCreate(
			    [
				    'email' => 'hayleybech@gmail.com',
			    ], [
			    'first_name' => 'Hayley',
			    'last_name' => 'Bech',
			    'password' => bcrypt(Str::random(30)),
		    ]),
		    new TenantCreated($this->tenant),
	    );
    }
}
