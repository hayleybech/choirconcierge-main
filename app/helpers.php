<?php

use Illuminate\Support\Carbon;

if (! function_exists('the_tenant_route')) {
    // Allows tenant_route to auto-fetch the domain
    function the_tenant_route($route, $parameters = [], $absolute = true)
    {
    	if(app()->environment('testing')) {
		    return tenant_route(tenant()->host, $route, $parameters, $absolute);
	    }
        return tenant_route(tenant()->primary_domain, $route, $parameters, $absolute);
    }
}

if (! function_exists('central_route')) {
    // Allows getting a central route while in a tenant
    function central_route($route, $parameters = [], $absolute = true)
    {
        return tenant_route(config('app.url'), $route, $parameters, $absolute);
    }
}

if (! function_exists('global_tenant_asset')) {
    function global_tenant_asset($tenant, $asset): string
    {
        return tenant_route($tenant->host, 'stancl.tenancy.asset', ['path' => $asset]);
    }
}

if (! function_exists('central_domain')) {
    function central_domain(): string
    {
        return parse_url(config('app.url'), PHP_URL_HOST);
    }
}

if (! function_exists('tz_from_tenant_to_utc')) {
    function tz_from_tenant_to_utc(string $date): Carbon
    {
        $timezone = tenant('timezone') ?? config('app.timezone');
        return Carbon::parse($date, $timezone)
            ->utc();
    }
}
if (! function_exists('tz_from_utc_to_tenant')) {
    function tz_from_utc_to_tenant(string $date): Carbon
    {
        $timezone = tenant('timezone') ?? config('app.timezone');
        return Carbon::parse($date)
            ->timezone($timezone);
    }
}