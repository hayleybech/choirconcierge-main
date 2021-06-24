<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class GroupMember
 *
 * Columns
 * @property int $id
 * @property int $group_id
 * @property string $memberable_type
 * @property string $memberable_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property UserGroup $group
 * @property User|Role $memberable
 *
 * @package App\Models
 */
class GroupMember extends Model
{
	use TenantTimezoneDates;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['memberable_id', 'memberable_type'];

	/**
	 * Get all of the member models (users, roles etc).
	 */
	public function memberable(): MorphTo
	{
		return $this->morphTo();
	}

	public function group(): BelongsTo
	{
		return $this->belongsTo(UserGroup::class);
	}
}
