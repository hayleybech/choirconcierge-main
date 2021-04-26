<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class VoicePart
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property string $colour
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Carbon $tenant_id
 *
 * Relationships
 * @property Collection<Singer> $singers
 * @property Collection<User> $users
 *
 * @package App\Models
 */
class VoicePart extends Model
{
    use BelongsToTenant, SoftDeletes, TenantTimezoneDates;

    protected $fillable = [
        'title',
        'colour',
    ];

    public function singers(): HasMany
    {
        return $this->hasMany( Singer::class );
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Singer::class);
    }
}
