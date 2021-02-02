<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
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
 * @property Event[] $events
 *
 * @package App\Models
 */
class EventType extends Model
{
    use BelongsToTenant, SoftDeletes;

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'type_id');
    }
}
