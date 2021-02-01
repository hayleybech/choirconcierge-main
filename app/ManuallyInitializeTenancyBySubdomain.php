<?php


namespace App;
use App\Models\Tenant;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\NotASubdomainException;
use Stancl\Tenancy\Exceptions\TenantCountNotBeIdentifiedById;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;


/**
 * @see InitializeTenancyBySubdomain
*/
class ManuallyInitializeTenancyBySubdomain
{
    /**
     * The index of the subdomain fragment in the hostname
     * split by `.`. 0 for first fragment, 1 if you prefix
     * your subdomain fragments with `www`.
     */
    public static int $subdomainIndex = 0;

    /**
     * @throws TenantCountNotBeIdentifiedById
     */
    public function handle(string $hostname): void
    {
        $subdomain = $this->makeSubdomain($hostname);

        tenancy()->initialize(Tenant::findByDomain($subdomain));
    }

    /** @return string|Response|Exception|mixed */
    protected function makeSubdomain(string $hostname)
    {
        $parts = explode('.', $hostname);

        $isLocalhost = count($parts) === 1;
        $isIpAddress = count(array_filter($parts, 'is_numeric')) === count($parts);

        // If we're on localhost or an IP address, then we're not visiting a subdomain.
        $isACentralDomain = in_array($hostname, config('tenancy.central_domains'), true);
        $notADomain = $isLocalhost || $isIpAddress;
        $thirdPartyDomain = !Str::endsWith($hostname, config('tenancy.central_domains'));

        if ($isACentralDomain || $notADomain || $thirdPartyDomain) {
            return new NotASubdomainException($hostname);
        }

        return $parts[static::$subdomainIndex];
    }
}