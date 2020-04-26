<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class VoicePart
 *
 * Columns
 * @property int id
 * @property string title
 *
 * Relationships
 * @property Singer[] $singers
 * @property User[] $users
 *
 * @package App\Models
 */
class VoicePart extends Model
{
    public function singers(): HasMany
    {
        return $this->hasMany( Singer::class );
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Singer::class);
    }
}
