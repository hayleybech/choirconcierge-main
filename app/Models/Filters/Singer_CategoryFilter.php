<?php
namespace App\Models\Filters;

use App\Models\Singer;
use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Singer_CategoryFilter
 * Filters Singers by Category
 *
 * @package App\Models\Filters
 */
class Singer_CategoryFilter extends Filter
{
    protected $name = 'filter_category';
    protected $label = 'Category';
    protected $default_option = 'any';

    protected function initOptions(): void
    {
        $categories = SingerCategory::all();
        foreach($categories as $category)
        {
            $this->options[$category->id] = $category->name;
        }

        $categories = SingerCategory::all();
        $categories_keyed = $categories->mapWithKeys(static function($item){
            return [ $item['id'] => $item['name'] ];
        });
        $categories_keyed->prepend('Any status','any');
        $this->options = $categories_keyed;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function run(Builder $query): Builder
    {
        // Category
        if( $this->current_option !== 'any' ) {
            return $query->where('singer_category_id', '=', $this->current_option );
        }

        // Any
        return $query;
    }
}