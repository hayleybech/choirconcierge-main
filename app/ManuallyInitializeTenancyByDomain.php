<?php

namespace App;

use App\Models\Tenant;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

/**
 * @see InitializeTenancyByDomain
 */
class ManuallyInitializeTenancyByDomain
{
	public function handle(string $hostname): void
	{
		tenancy()->initialize(Tenant::findByDomain($hostname));
	}
}
