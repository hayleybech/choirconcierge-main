<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SongAttachmentCategory extends Model
{
    public function attachment() {
        return $this->hasMany('App\SongAttachment');
    }
}
