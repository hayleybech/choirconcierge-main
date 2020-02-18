<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SongAttachmentCategory extends Model
{
    public function attachments() {
        return $this->hasMany('App\SongAttachment', 'category_id');
    }
}
