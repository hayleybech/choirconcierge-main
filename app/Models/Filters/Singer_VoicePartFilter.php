<?php
namespace App\Models\Filters;

use App\Models\Singer;
use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
        $this->options = [
            'any'   => 'Any part',
            'tenor' => 'Tenor',
            'lead'  => 'Lead',
            'bari'  => 'Baritone',
            'bass'  => 'Bass',
        ];
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function run(Builder $query): Builder
    {
        // Voice Part
        if($this->current_option !== 'any') {
            return $query->whereHas('placement', function(Builder $query) {
                $query->where('voice_part', '=', $this->current_option);
            });
        }

        // Any
        return $query;
    }
}