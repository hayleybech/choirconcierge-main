<?php
namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Singer_AgeFilter
 * Filters Singers by Age group
 *
 * @package App\Models\Filters
 */
class Singer_AgeFilter extends Filter
{
    protected $name = 'filter_age';
    protected $label = 'Age';
    protected $default_option = 'any';
    private const ADULT_AGE = 18;

    protected function initOptions(): void
    {
        $this->options = [
            'any'    => 'Any age',
            'adult'  => 'Over ' . self::ADULT_AGE,
            'child'  => 'Under ' . self::ADULT_AGE,
        ];
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function run(Builder $query): Builder
    {
        // Adult
        if( $this->current_option === 'adult' ) {
            return $query->whereHas('profile', static function(Builder $query) {
                $query->where('dob', '<=', date('Y-m-d', strtotime('-'.self::ADULT_AGE. ' years')));
            });
        }
        // Child
        if( $this->current_option === 'child' ) {
            return $query->whereHas('profile', static function(Builder $query) {
                $query->where('dob', '>', date('Y-m-d', strtotime('-'.self::ADULT_AGE. ' years')));
            });
        }

        // Any
        return $query;
    }
}