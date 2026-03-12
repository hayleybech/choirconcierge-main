<?php

namespace App\Traits;

use App\CustomSorts\SingerNameSort;
use App\CustomSorts\SingerLastNameFirstSort;
use Spatie\QueryBuilder\AllowedSort;

trait HasSingerSorts
{
    protected function singerSorts(): array
    {
        return [
            AllowedSort::custom('full-name', new SingerNameSort(), 'name'),
            AllowedSort::custom('last-name-first', new SingerLastNameFirstSort(), 'last_name'),
        ];
    }
}
