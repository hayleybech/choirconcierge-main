<?php

namespace App\CustomSorts;

use App\Models\SingerStatus;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SingerStatusSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        return $query
            ->addSubSelect(
                'status_title',
                SingerStatus::select('name')
                    ->join('membership_singer_status', 'singer_statuses.id', '=', 'membership_singer_status.singer_status_id')
                    ->whereColumn('membership_singer_status.membership_id', 'memberships.id')
                    ->orderByDesc('membership_singer_status.id')
                    ->limit(1)
            )
            ->orderBy('status_title', $descending ? 'desc' : 'asc');
    }
}
