<?php

use Illuminate\Support\Carbon;

if (! function_exists('the_tenant_route')) {
    // Allows tenant_route to auto-fetch the domain
    function the_tenant_route($route, $parameters = [], $absolute = true)
    {
		$parameters = [
			'tenant' => tenant('id'),
			...(is_array($parameters) ? $parameters : [$parameters]),
		];
        if (! app()->environment(['production', 'local'])) {
            return route($route, $parameters, $absolute);
        }

        return route($route, $parameters, $absolute);
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

        return Carbon::parse($date, $timezone)->utc();
    }
}
if (! function_exists('tz_from_utc_to_tenant')) {
    function tz_from_utc_to_tenant(string $date): Carbon
    {
        $timezone = tenant('timezone') ?? config('app.timezone');

        return Carbon::parse($date)->timezone($timezone);
    }
}

if (! function_exists('checkbox_old')) {
    function checkbox_old(string $input_name, bool $default_value, bool $saved_value = null): bool
    {
        if (empty(old())) {
            return $saved_value ?? $default_value;
        }

        return (bool) old($input_name, $default_value);
    }
}

if (! function_exists('checkbox_group_old')) {
    function checkbox_group_old(string $input_name, string $this_value, array $saved_values = []): bool
    {
        if (empty(old())) {
            return in_array($this_value, $saved_values, false);
        }

        return in_array($this_value, old($input_name) ?? [], false);
    }
}

if (! function_exists('radio_old')) {
    function radio_old(
        string $group_name,
        string $this_value,
        string $default_group_value,
        string $saved_group_value = null
    ): bool {
        if (empty(old())) {
            return ($saved_group_value ?? $default_group_value) === $this_value;
        }

        return $this_value === (string) old($group_name);
    }
}
