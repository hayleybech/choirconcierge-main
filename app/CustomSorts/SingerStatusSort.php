<?php

namespace App\CustomSorts;

use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SingerStatusSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $prefix = \DB::getTablePrefix();

        return $query
            ->addSubSelect(
                'status_title',
                SingerCategory::select('name')
                    ->whereRaw("`${prefix}singers`.`singer_category_id` = `${prefix}singer_categories`.`id`")
            )
            ->orderBy('status_title', $descending ? 'desc' : 'asc');
    }
}
