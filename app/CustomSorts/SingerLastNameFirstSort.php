<?php

namespace App\CustomSorts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SingerLastNameFirstSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $prefix = \DB::getTablePrefix();

        return $query
            ->addSubSelect('last_name_first',
                User::selectRaw('CONCAT(last_name, " ", first_name) AS last_name_first')
                    ->whereRaw("`${prefix}memberships`.`user_id` = `${prefix}users`.`id`")
            )
            ->orderBy('last_name_first', $descending ? 'desc' : 'asc');
    }
}
