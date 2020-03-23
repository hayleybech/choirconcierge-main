<?php


namespace App\Models\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

/***
 * Class Filter
 * Extend this class to create Filters for Models.
 *
 * @package App\Models\Filters
 */
abstract class Filter
{
    /** @var string $name The human-readable Filter name. */
    protected $name;

    /** @var string $label The label to use for the form field. */
    protected $label;

    /** @var string $default_option The option selected by default. */
    protected $default_option;

    /** @var string $current_option The option selected by the user. */
    protected $current_option;

    /** @var array $options All the options defined by this filter (as label => value). */
    protected $options = [];

    public function __construct() {
        $this->initOptions();
        $this->current_option = request()->input($this->name, $this->default_option);
    }

    /**
     * Set up the options array (as label => value)
     */
    abstract protected function initOptions(): void;

    /**
     * The actual query occurs here
     * @param Builder $query
     * @return Builder
     */
    abstract protected function run( Builder $query ): Builder;

    public function __get( $name ) {
        return $this->$name;
    }

    /**
     * The main entry point for models to apply their filters
     * This method prepares and call run().
     *
     * @param Builder  $query
     *
     * @return Builder
     */
    public function apply( Builder $query ): Builder
    {
        if( $this->current_option !== 'any' ) {
            return $this->run( $query );
        }
        return $query;
    }

    public function isDefault(): bool {
        return $this->current_option !== $this->default_option;
    }

    /**
     * Generates the "selected" attribute for an option tag
     * @param string $this_value The value of the tag we're generating
     * @param string $selected_value The value selected by the user, to compare.
     * @return string
     */
    private static function selected( string $this_value, string $selected_value ): string {
        return $this_value === $selected_value ? 'selected' : '';
    }
}