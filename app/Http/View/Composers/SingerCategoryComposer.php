<?php

namespace App\Http\View\Composers;

use App\Models\SingerCategory;
use Illuminate\Contracts\View\View;

class SingerCategoryComposer
{
    private $categories;

    /**
     * @return mixed
     */
    public function getCategories()
    {
        if (! $this->categories) {
            $categories_all = SingerCategory::all();
            $this->categories = $categories_all->mapWithKeys(static function ($category) {
                return [$category['id'] => $category['name']];
            });
        }

        return $this->categories;
    }

    public function compose(View $view): View
    {
        return $view->with('singer_categories', $this->getCategories());
    }
}
