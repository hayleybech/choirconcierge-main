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

    protected $name = 'filter_date';
    protected $label = 'Date Range';
    protected $default_option = 'any';

    protected ?Carbon $start_date;
    protected ?Carbon $end_date;

    protected function initOptions(): void
    {
        $this->start_date = isset($this->current_option['start']) ? Carbon::create($this->current_option['start']) : Carbon::today()->subMonth();
        $this->end_date = isset($this->current_option['start']) ? Carbon::create($this->current_option['end']) : Carbon::today();
    }

    public function render(): void
    {
        echo view('partials.filters.date', ['filter' => $this]);
    }

    protected function run(Builder $query): Builder
    {
        return $query->where('start_date', '>', $this->start_date)
            ->where('end_date', '<', $this->end_date);
    }
}