<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class SingerCategory
 *
 * Columns
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<Singer> $singers
 *
 * @package App\Models
 */
class SingerCategory extends Model
{
    use BelongsToTenant, SoftDeletes;
    
    public function singers(): HasMany
    {
        return $this->hasMany(Singer::class);
    }
}
