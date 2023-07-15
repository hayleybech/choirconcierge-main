<?php

namespace App\CustomSorts;

use App\Models\SingerCategory;
use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SingerVoicePartSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $prefix = \DB::getTablePrefix();

        return $query
            ->addSubSelect(
                'part_title',
                VoicePart::select('title')
                    ->whereRaw("`${prefix}memberships`.`voice_part_id` = `${prefix}voice_parts`.`id`")
            )
            ->orderBy('part_title', $descending ? 'desc' : 'asc');
    }
}
