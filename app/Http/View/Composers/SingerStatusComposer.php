<?php

namespace App\Http\View\Composers;

use App\Enums\SingerStatus;
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
            $this->categories = collect(SingerStatus::cases())->mapWithKeys(static function ($category) {
                return [$category->value => $category->label()];
            });
        }

        return $this->categories;
    }

    public function compose(View $view): View
    {
        return $view->with('singer_statuses', $this->getCategories());
    }
}
