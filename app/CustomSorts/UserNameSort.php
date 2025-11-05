<?php

namespace App\CustomSorts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class UserNameSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        return $query
            ->orderBy('first_name', $descending ? 'desc' : 'asc')
            ->orderBy('last_name', $descending ? 'desc' : 'asc');
    }
}
