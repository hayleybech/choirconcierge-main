<?php


namespace App\Models\Filters;


use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /** @var Filter[] */
    protected static $filters = [];

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFilter(Builder $query): Builder
    {
        // Apply filters one at a time
        foreach(static::getFilters() as $filter) {
            $query = $filter->apply($query);
        }

        return $query;
    }

    /**
     * Set up the static filters array for this Model
     */
    abstract public static function initFilters(): void;

    /**
     * Get the Filters for this Model
     * @return array
     */
    public static function getFilters(): array {
        // Cache results
        if( count(self::$filters) === 0 ) {
            static::initFilters();
        }

        return self::$filters;
    }

    public static function getFilterQueryString(): string {
        $query_string = '';
        foreach( self::$filters as $key => $filter ) {
            $query_string .= $filter->name . '=' . $filter->current_option;

            if ( $key !== array_key_last(self::$filters) ) {
                $query_string .= '&';
            }
        }
        return $query_string;
    }
}