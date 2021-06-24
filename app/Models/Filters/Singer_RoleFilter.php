<?php
namespace App\Models\Filters;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Singer_RoleFilter
 * Filters Singers by User Role
 *
 * @package App\Models\Filters
 */
class Singer_RoleFilter extends Filter
{
	protected string $name = 'filter_role';
	protected string $label = 'Role';
	protected string $default_option = 'any';

	protected function initOptions(): void
	{
		$this->options =
			['0' => 'Any role'] +
			Role::all()
				->pluck('name', 'id')
				->toArray();
	}

	protected function run(Builder $query): Builder
	{
		// Role
		if ($this->current_option !== '0') {
			return $query->whereHas('user', function (Builder $subquery1) {
				$subquery1->whereHas('roles', function (Builder $subquery2) {
					$subquery2->where('id', '=', $this->current_option);
				});
			});
		}

		// Any
		return $query;
	}
}
