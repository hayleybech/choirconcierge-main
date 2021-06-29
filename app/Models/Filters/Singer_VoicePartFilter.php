<?php
namespace App\Models\Filters;

use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Singer_VoicePartFilter
 * Filters Singers by Voice Part
 *
 * @package App\Models\Filters
 */
class Singer_VoicePartFilter extends Filter
{
	protected string $name = 'filter_part';
	protected string $label = 'Part';
	protected string $default_option = 'any';

	protected function initOptions(): void
	{
		$this->options =
			['0' => 'Any part'] +
			VoicePart::all()
				->pluck('title', 'id')
				->toArray();
	}

	/**
	 * @param Builder $query
	 * @return Builder
	 */
	protected function run(Builder $query): Builder
	{
		// Voice Part
		if ($this->current_option !== '0') {
			return $query->where('voice_part_id', '=', $this->current_option);
		}

		// Any
		return $query;
	}
}
