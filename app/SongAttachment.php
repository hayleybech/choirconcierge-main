<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SongAttachment extends Model
{
    public function song() {
        return $this->belongsTo('App\Song');
    }

    public function category() {
        return $this->belongsTo('App\SongAttachmentCategory', 'category_id');
    }
}
