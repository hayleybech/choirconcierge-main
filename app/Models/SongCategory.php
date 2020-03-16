<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SongCategory extends Model
{
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Song', 'songs_song_categories', 'category_id', 'song_id');
    }
}