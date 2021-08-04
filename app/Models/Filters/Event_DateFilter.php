<?php

namespace App\Models\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Event_DateFilter
 * Filters Events by Start Date
 */
class Event_DateFilter extends Filter
{
	protected string $name = 'filter_date';
	protected string $label = 'Date Range';
	protected string $default_option = 'any';

	protected ?Carbon $start_date;
	protected ?Carbon $end_date;

	protected function initOptions(): void
	{
		$this->start_date = isset($this->current_option['start'])
			? Carbon::create($this->current_option['start'])
			: Carbon::today()->subMonth();
		$this->end_date = isset($this->current_option['start'])
			? Carbon::create($this->current_option['end'])
			: Carbon::today();
	}

	public function render(): void
	{
		echo view('partials.filters.date', ['filter' => $this]);
	}

	protected function run(Builder $query): Builder
	{
		return $query->where('call_time', '>', $this->start_date)
            ->where('end_date', '<', $this->end_date);
	}
}
