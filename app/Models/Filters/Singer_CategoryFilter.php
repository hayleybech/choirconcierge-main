<?php
namespace App\Models\Filters;

use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Singer_CategoryFilter
 * Filters Singers by Category
 *
 * @package App\Models\Filters
 */
class Singer_CategoryFilter extends Filter
{
	protected string $name = 'filter_category';
	protected string $label = 'Category';
	protected string $default_option = 'any';

	protected function initOptions(): void
	{
		$categories = SingerCategory::all();
		foreach ($categories as $category) {
			$this->options[$category->id] = $category->name;
		}

		$categories = SingerCategory::all();
		$this->options = $categories->mapWithKeys(static function ($item) {
			return [$item['id'] => $item['name']];
		});
		$this->options->prepend('Any status', 'any');
	}

	protected function run(Builder $query): Builder
	{
		// Category
		if ($this->current_option !== 'any') {
			return $query->where('singer_category_id', '=', $this->current_option);
		}

		// Any
		return $query;
	}
}
