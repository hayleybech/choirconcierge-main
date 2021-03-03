<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Alert extends Component
{
    public string $variant;

    public string $title;

    private string $icon;

    protected static int $instances = 0;

    public int $id;

    /**
     * Create a new component instance.
     * @param string $variant
     * @param string $title
     * @param string $icon
     */
    public function __construct(string $variant = 'info', string $title = '', string $icon = '')
    {
        self::$instances++;
        $this->id = self::$instances;

        $this->variant = $variant;
        $this->title = $title;
        $this->icon = $icon;
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
        if($this->icon) {
            return $this->icon;
        }

        $icons = [
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-square',
            'danger'  => 'fa-exclamation-triangle',
            'info'    => 'fa-question-circle',
        ];
        return $icons[$this->variant];
    }
}
