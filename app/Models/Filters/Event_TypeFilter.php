<?php
namespace App\Models\Filters;

use App\Models\EventType;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Event_TypeFilter
 * Filters Events by Type
 *
 * @package App\Models\Filters
 */
class Event_TypeFilter extends Filter
{
    protected string $name = 'filter_type';
    protected string $label = 'Type';
    protected string $default_option = 'any';

    protected function initOptions(): void
    {
        $types = EventType::all();
        $this->options = $types->mapWithKeys(static function($item){
            return [ $item['id'] => $item['title'] ];
        });
        $this->options->prepend('Any type','any');
    }

    protected function run(Builder $query): Builder
    {
        // Type
        if( $this->current_option !== 'any' ) {
            return $query->where('type_id', '=', $this->current_option);
        }

        // Any
        return $query;
    }
}