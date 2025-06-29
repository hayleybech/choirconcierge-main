<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use MenaraSolutions\Geographer\Earth;

class CountryFromCca3 implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string $key
     * @param  string $value The country ID in cca3 format
     * @param  array  $attributes
     * @return mixed A Country object
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if(! $value) {
            return null;
        }
        return (new Earth())->findOne(['code3' => $value]);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string $key
     * @param  mixed  $value The country object
     * @param  array  $attributes
     * @return string The country ID in cca3 format
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        if(is_string($value)){
            return $value;
        }

        if(! $value) {
            return '';
        }

        return $value->code3;
    }
}
