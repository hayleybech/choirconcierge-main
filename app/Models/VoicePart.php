<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VoicePart extends Model
{
    public function placement(): HasOne
    {
        return $this->hasOne( Placement::class );
    }
}
