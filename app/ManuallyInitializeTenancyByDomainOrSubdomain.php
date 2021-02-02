<?php

namespace App;

use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

/**
 * @see InitializeTenancyByDomainOrSubdomain
 */
class ManuallyInitializeTenancyByDomainOrSubdomain
{
    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(string $hostname): void
    {
        if ($this->isSubdomain($hostname)) {
            app(ManuallyInitializeTenancyBySubdomain::class)->handle($hostname);
            return;
        }

        app(ManuallyInitializeTenancyByDomain::class)->handle($hostname);
    }

    protected function isSubdomain(string $hostname): bool
    {
        return Str::endsWith($hostname, config('tenancy.central_domains')) && ! Str::startsWith($hostname, config('tenancy.central_domains'));
    }
}