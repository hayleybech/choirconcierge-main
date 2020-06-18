<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class DeleteButton extends Component
{
    public $action;

    /**
     * Create a new component instance.
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('components.delete-button');
    }
}
