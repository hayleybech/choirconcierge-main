<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SingerCategory extends Model
{
    public function singers(): HasMany
    {
        return $this->hasMany('App\Models\Singer');
    }
}
