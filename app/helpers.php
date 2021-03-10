<?php
if (! function_exists('the_tenant_route')) {
    // Allows tenant_route to auto-fetch the domain
    function the_tenant_route($route, $parameters = [], $absolute = true)
    {
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