<?php

namespace App\Models\Filters;

use Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/***
 * Class Filter
 * Extend this class to create Filters for Models.
 *
 * @property string $name The human-readable Filter name.
 * @property string $label The label to use for the form field.
 * @property string $default_option The option selected by default.
 * @property string|array $current_option The option(s) selected by the user.
 * @property array|Collection $options All the options defined by this filter (as label => value).
 *
 * @package App\Models\Filters
 */
abstract class Filter
{
	protected string $name;

	protected string $label;

	protected string $default_option;

	protected string|array $current_option;

	protected array|Collection $options = [];

	public function __construct()
	{
		$this->current_option = request()->input($this->name, $this->default_option);
		$this->initOptions();
	}

	/**
	 * Set up the options array (as label => value)
	 */
	abstract protected function initOptions(): void;

	public function render(): void
	{
		$field_class = $this->isDefault() ? '' : 'border-primary';
		echo Form::select($this->name, $this->options, $this->current_option, [
			'class' => 'custom-select form-control-sm ' . $field_class,
		]);
	}

	/**
	 * The actual query occurs here
	 * @param Builder $query
	 * @return Builder
	 */
	abstract protected function run(Builder $query): Builder;

	public function __get($name)
	{
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
	public function apply(Builder $query): Builder
	{
		if ($this->current_option !== 'any') {
			return $this->run($query);
		}
		return $query;
	}

	public function isDefault(): bool
	{
		return $this->current_option === $this->default_option;
	}

	/**
	 * Generates the "selected" attribute for an option tag
	 * @param string $this_value The value of the tag we're generating
	 * @param string $selected_value The value selected by the user, to compare.
	 * @return string
	 */
	private static function selected(string $this_value, string $selected_value): string
	{
		return $this_value === $selected_value ? 'selected' : '';
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getCurrentOption(): string
	{
		return is_string($this->current_option) ? $this->current_option : http_build_query($this->current_option);
	}
}
