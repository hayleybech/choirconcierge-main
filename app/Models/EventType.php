<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventType extends Model
{
    public function events(): HasMany
    {
        return $this->hasMany('App\Models\Event', 'type_id');
    }
}
