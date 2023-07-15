<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class RiserStack
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property int $rows
 * @property int $columns
 * @property int $front_row_length
 * @property bool $front_row_on_floor
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property int $tenant_id
 *
 * Relationships
 * @property Collection<Membership> $members
 */
class RiserStack extends Model
{
    use BelongsToTenant, SoftDeletes, TenantTimezoneDates, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'rows', 'columns', 'front_row_length', 'front_row_on_floor'];

    protected $casts = ['front_row_on_floor' => 'boolean'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Membership::class, 'riser_stack_membership', 'riser_stack_id', 'membership_id')
            ->as('position')
            ->withPivot('row', 'column');
    }
}
