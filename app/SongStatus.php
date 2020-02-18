<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongStatus extends Model
{
    public function songs(): HasMany
    {
        return $this->hasMany('App\Song', 'status_id');
    }
}
