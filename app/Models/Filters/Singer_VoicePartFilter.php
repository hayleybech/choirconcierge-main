<?php
namespace App\Models\Filters;

use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Singer_VoicePartFilter
 * Filters Singers by Voice Part
 *
 * @package App\Models\Filters
 */
class Singer_VoicePartFilter extends Filter
{
    protected $name = 'filter_part';
    protected $label = 'Part';
    protected $default_option = 'any';

    protected function initOptions(): void
    {
        $parts = VoicePart::all()->pluck('title', 'id')->toArray();

        $this->options = array_merge([
            '0'   => 'Any part',
        ], $parts);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function run(Builder $query): Builder
    {
        // Voice Part
        if($this->current_option !== '0') {
            return $query->whereHas('placement', function(Builder $query) {
                $query->where('voice_part', '=', $this->current_option);
            });
        }

        // Any
        return $query;
    }
}