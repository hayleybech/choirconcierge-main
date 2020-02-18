<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongAttachmentCategory extends Model
{
    public function attachments(): HasMany
    {
        return $this->hasMany('App\SongAttachment', 'category_id');
    }
}
