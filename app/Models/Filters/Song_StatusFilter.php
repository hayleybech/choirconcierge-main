<?php
namespace App\Models\Filters;

use App\Models\SongStatus;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Song_StatusFilter
 * Filters Songs by Status
 *
 * @package App\Models\Filters
 */
class Song_StatusFilter extends Filter
{
    protected string $name = 'filter_status';
    protected string $label = 'Status';
    protected string $default_option = 'any';

    protected function initOptions(): void
    {
        $statuses = SongStatus::all();
        $this->options = $statuses->mapWithKeys(static function($item){
            return [ $item['id'] => $item['title'] ];
        });
        $this->options->prepend('Any status','any');
    }

    protected function run(Builder $query): Builder
    {
        // Status
        if( $this->current_option !== 'any' ) {
            return $query->where('status_id', '=', $this->current_option);
        }

        // Any
        return $query;
    }
}