<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Alert extends Component
{
    public string $variant;

    public string $title;

    protected static int $instances = 0;

    public int $id;

    /**
     * Create a new component instance.
     * @param string $variant
     * @param string $title
     */
    public function __construct(string $variant = 'info', string $title = '')
    {
        self::$instances++;
        $this->id = self::$instances;

        $this->variant = $variant;
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render(): string|View
    {
        return view('components.alert');
    }

    public function icon(): string {
        $icons = [
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-square',
            'danger'  => 'fa-exclamation-triangle',
            'info'    => 'fa-question-circle',
        ];
        return $icons[$this->variant];
    }
}
