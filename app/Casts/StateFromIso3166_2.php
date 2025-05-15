<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PragmaRX\Countries\Package\Countries;

class StateFromIso3166_2 implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  string  $value The state ID in ISO 3166-2 format
     * @param  array  $attributes
     * @return mixed A State object
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if(! $value) {
            return null;
        }

        return Countries::firstWhere('cca2', self::cca2FromIso3166_2($value))
            ->hydrateStates()
            ->states
            ->firstWhere('iso_3166_2', $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if(is_string($value)) {
            return $value;
        }

        return $value->iso_3166_2;
    }

    /**
     * Converts ISO 3166-2 to ISO 3166-1 alpha-3 (aka Country Code Alpha 2, CCA2)
     */
    public static function cca2FromIso3166_2(string $value): string
    {
        return Str::of($value)->explode('-')->firstOrFail();
    }
}
