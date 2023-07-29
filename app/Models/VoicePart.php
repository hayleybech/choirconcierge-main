<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property Collection<Enrolment> $enrolments
 */
class VoicePart extends Model
{
    use BelongsToTenant, SoftDeletes, TenantTimezoneDates, HasFactory;

    protected $fillable = ['title', 'colour'];

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public static function getNullVoicePart(): self
    {
        $nullPart = new self();
        $nullPart->title = 'No Part';
        $nullPart->id = null;
        $nullPart->colour = 'gray';

        return $nullPart;
    }
}
