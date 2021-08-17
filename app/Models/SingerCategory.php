<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
	use BelongsToTenant, SoftDeletes, TenantTimezoneDates, HasFactory;

	protected $appends = ['colour'];

    public const CATEGORY_COLOURS = [
        'Members' => 'success',
        'Prospects' => 'warning',
        'Archived Prospects' => 'dark',
        'Archived Members' => 'tertiary',
    ];

	public const CATEGORY_COLOURS_REBUILD = [
		'Members' => 'green-500',
		'Prospects' => 'yellow-500',
		'Archived Prospects' => 'yellow-700',
		'Archived Members' => 'green-700',
	];

	public function singers(): HasMany
	{
		return $this->hasMany(Singer::class);
	}

	public function getColourAttribute(): string
	{
        if(config('features.rebuild')){
            return self::CATEGORY_COLOURS_REBUILD[$this->name];
        }
        return self::CATEGORY_COLOURS[$this->name];
    }
}
