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
 * Attributes
 * @property string $colour
 *
 * @package App\Models
 */
class SingerCategory extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const CATEGORY_COLOURS = [
        'Members' => 'success',
        'Prospects' => 'warning',
        'Archived Prospects' => 'dark',
        'Archived Members' => 'tertiary',
    ];

    public function singers(): HasMany
    {
        return $this->hasMany(Singer::class);
    }

    public function getColourAttribute(): string {
        return self::CATEGORY_COLOURS[$this->name];
    }
}
