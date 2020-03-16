<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongStatus extends Model
{
    public function songs(): HasMany
    {
        return $this->hasMany('App\Models\Song', 'status_id');
    }
}
