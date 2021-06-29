<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class GroupSender
 *
 * Columns
 *
 * @property int $id
 * @property int $group_id
 * @property string $sender_type
 * @property string $sender_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property UserGroup $group
 * @property User|Role|VoicePart|SingerCategory $sender
 */
class GroupSender extends Model
{
	use TenantTimezoneDates;

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = ['sender_id', 'sender_type'];

	/**
	 * Get all of the sender models (users, roles etc).
	 */
	public function sender(): MorphTo
	{
		return $this->morphTo();
	}

	public function group(): BelongsTo
	{
		return $this->belongsTo(UserGroup::class);
	}
}
