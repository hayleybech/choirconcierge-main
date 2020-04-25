<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class VoicePart
 *
 * Columns
 * @property int id
 * @property string title
 *
 * Relationships
 * @property Singer[] $singer
 *
 * @package App\Models
 */
class VoicePart extends Model
{
    public function singer(): HasMany
    {
        return $this->hasMany( Singer::class );
    }
}
