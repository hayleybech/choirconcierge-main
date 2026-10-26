<?php

namespace App\CustomSorts;

use App\Models\EventType;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class EventTypeSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $prefix = \DB::getTablePrefix();

        $query
            ->addSubSelect(
                'type_title',
                EventType::select('title')
                    ->whereRaw("`${prefix}events`.`type_id` = `${prefix}event_types`.`id`")
            )
            ->orderBy('type_title', $descending ? 'desc' : 'asc');
    }
}
