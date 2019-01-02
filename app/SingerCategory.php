<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SingerCategory extends Model
{
    public function singers()
    {
        return $this->hasMany('App\Singer');
    }
}
