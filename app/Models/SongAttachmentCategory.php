<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongAttachmentCategory extends Model
{
    public function attachments(): HasMany
    {
        return $this->hasMany('App\Models\SongAttachment', 'category_id');
    }
}
