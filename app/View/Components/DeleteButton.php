<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class DeleteButton extends Component
{
    public string $action;

    public string $message;

    /**
     * Create a new component instance.
     *
     * @param string $action
     * @param string $message
     */
    public function __construct(string $action, string $message = 'Do you really want to delete this record?')
    {
        $this->action = $action;
        $this->message = $message;
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
