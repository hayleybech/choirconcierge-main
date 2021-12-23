<?php

namespace App\CustomSorts;

use App\Models\SongStatus;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SongStatusSort implements Sort
{

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $prefix = \DB::getTablePrefix();
        return $query
            ->addSubSelect(
                'status_title',
                SongStatus::select('title')
                    ->whereRaw("`${prefix}songs`.`status_id` = `${prefix}song_statuses`.`id`")
            )
            ->orderBy('status_title', $descending ? 'desc' : 'asc');
    }
}