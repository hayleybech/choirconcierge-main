<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
	/** @var string[] $filters The filter classes to use for the extending Model. */
	//protected static $filters = []; Define in the extending/using Model

	/** @var Filter[] $_filters The instantiated filter objects */
	protected static array $_filters = [];

	public function scopeFilter(Builder $query): Builder
	{
		// Apply filters one at a time
		foreach (static::getFilters() as $filter) {
			$query = $filter->apply($query);
		}

		return $query;
	}

	/**
	 * Set up the static filters array for this Model
	 */
	public static function initFilters(): void
	{
		foreach (static::$filters as $filter_class) {
			self::$_filters[] = new $filter_class();
		}
	}

	/**
	 * Get the Filters for this Model
	 */
	public static function getFilters(): array
	{
		// Cache results
		if (count(self::$_filters) === 0) {
			static::initFilters();
		}

		return self::$_filters;
	}

	public static function getFilterQueryString(): string
	{
		$query_string = '';
		foreach (self::$_filters as $key => $filter) {
			$query_string .= $filter->getName() . '=' . $filter->getCurrentOption();

			if ($key !== array_key_last(self::$_filters)) {
				$query_string .= '&';
			}
		}
		return $query_string;
	}

	/**
	 * Checks if any filter has been changed from the default
	 */
	public static function hasActiveFilters(): bool
	{
		foreach (self::$_filters as $filter) {
			if (!$filter->isDefault()) {
				return true;
			}
		}

		return false;
	}
}
