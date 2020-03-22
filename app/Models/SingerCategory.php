<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class SingerCategory
 *
 * Columns
 * @property int $id
 * @property string name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Singer[] $singers
 *
 * @package App\Models
 */
class SingerCategory extends Model
{
    public function singers(): HasMany
    {
        return $this->hasMany(Singer::class);
    }
}
