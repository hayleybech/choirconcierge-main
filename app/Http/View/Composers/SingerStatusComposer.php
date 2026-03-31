<?php

namespace App\Http\View\Composers;

use App\Models\SingerStatus;
use Illuminate\Contracts\View\View;

class SingerStatusComposer
{
    private $categories;

    /**
     * @return mixed
     */
    public function getCategories()
    {
        if (! $this->categories) {
            $categories_all = SingerStatus::all();
            $this->categories = $categories_all->mapWithKeys(static function ($category) {
                return [$category['id'] => $category['name']];
            });
        }

        return $this->categories;
    }

    public function compose(View $view): View
    {
        return $view->with('singer_statuses', $this->getCategories());
    }
}
