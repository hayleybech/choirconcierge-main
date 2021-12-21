<?php

namespace App\CustomSorts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SingerNameSort implements Sort
{

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $prefix = \DB::getTablePrefix();
        return $query
            ->addSubSelect('full_name',
                User::selectRaw('CONCAT(first_name, " ", last_name) AS full_name')
                    ->whereRaw("`${prefix}singers`.`user_id` = `${prefix}users`.`id`")
            )
            ->orderBy('full_name', $descending ? 'desc' : 'asc');
    }
}