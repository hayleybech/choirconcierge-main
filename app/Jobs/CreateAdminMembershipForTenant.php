<?php

namespace App\Jobs;

use App\Models\Membership;
use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateAdminMembershipForTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Tenant $tenant)
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    $admin = $this->getAdminUser($this->tenant->created_by);

		$member = Membership::create([
			'user_id' => $admin->id,
			'onboarding_enabled' => false,
			'joined_at' => now(),
			'user_roles' => [
				Role::query()
					->where('tenant_id', $this->tenant->id)
					->where('name', 'Admin')
					->valueOrFail('id'),
			],
		    'singer_category_id' => SingerCategory::query()
			    ->where('tenant_id', $this->tenant->id)
			    ->where('name', '=', 'Members')
			    ->valueOrFail('id')
		]);

	    $this->tenant->members()->save($member);
    }

	public function getAdminUser(?string $adminId)
	{
		if ($adminId) {
			return User::findOrFail($adminId);
		}

		return User::firstOrCreate(
			[
				'email' => 'hayleybech@gmail.com',
			], [
				'first_name' => 'Hayley',
				'last_name' => 'Bech',
				'password' => bcrypt(Str::random(30)),
			]
		);
	}
}
