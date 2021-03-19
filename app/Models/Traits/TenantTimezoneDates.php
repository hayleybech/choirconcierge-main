<?php


namespace App\Models\Traits;


use Illuminate\Support\Carbon;

trait TenantTimezoneDates
{
    public function getCreatedAtAttribute($value): Carbon
    {
        return tz_from_utc_to_tenant($value);
    }

    public function getUpdatedAtAttribute($value): Carbon
    {
        return tz_from_utc_to_tenant($value);
    }
}