<?php


namespace App\Models\Traits;


use Illuminate\Support\Carbon;

trait TenantTimezoneDates
{
    public function getCreatedAtAttribute(?string $value): ?Carbon
    {
        return $value ? tz_from_utc_to_tenant($value) : null;
    }

    public function getUpdatedAtAttribute(?string $value): ?Carbon
    {
        return $value ? tz_from_utc_to_tenant($value) : null;
    }
}