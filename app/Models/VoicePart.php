<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class VoicePart
 *
 * Columns
 * @property int id
 * @property string title
 * @property string colour
 *
 * Relationships
 * @property Singer[] $singers
 * @property User[] $users
 *
 * @package App\Models
 */
class VoicePart extends Model
{
    use BelongsToTenant, SoftDeletes;

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
