<?php
namespace App\Models\Filters;

use App\Models\SongCategory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Song_CategoryFilter
 * Filters Songs by Category
 *
 * @package App\Models\Filters
 */
class Song_CategoryFilter extends Filter
{
    protected string $name = 'filter_category';
    protected string $label = 'Category';
    protected string $default_option = 'any';

    protected function initOptions(): void
    {
        $categories = SongCategory::all();
        $this->options = $categories->mapWithKeys(static function($item){
            return [ $item['id'] => $item['title'] ];
        });
        $this->options->prepend('Any category','any');
    }

    protected function run(Builder $query): Builder
    {
        // Category
        if( $this->current_option !== 'any' ) {
            return $query->whereHas('categories', function (Builder $query) {
                $query->where('song_categories.id', '=', $this->current_option);
            });
        }

        // Any
        return $query;
    }
}