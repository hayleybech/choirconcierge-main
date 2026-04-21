<?php

namespace App\CustomSorts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class SingerStatusSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        return $query
            ->addSubSelect(
                'status_title',
                DB::table('membership_status')
                    ->select('status')
                    ->whereColumn('membership_id', 'memberships.id')
                    ->orderByDesc('id')
                    ->limit(1)
            )
            ->orderBy('status_title', $descending ? 'desc' : 'asc');
    }
}
