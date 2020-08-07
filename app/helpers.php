<?php
if (! function_exists('the_tenant_route')) {
    // Allows tenant_route to auto-fetch the domain
    function the_tenant_route($route, $parameters = [], $absolute = true)
    {
        return tenant_route(tenant()->domains->first()->domain, $route, $parameters, $absolute);
    }
}

if (! function_exists('central_route')) {
    // Allows getting a central route while in a tenant
    function central_route($route, $parameters = [], $absolute = true)
    {
        return tenant_route(config('app.url'), $route, $parameters, $absolute);
    }
}