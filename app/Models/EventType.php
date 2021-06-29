<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class EventType
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<Event> $events
 *
 * @package App\Models
 */
class EventType extends Model
{
	use BelongsToTenant, SoftDeletes, TenantTimezoneDates;

	public function events(): HasMany
	{
		return $this->hasMany(Event::class, 'type_id');
	}
}
