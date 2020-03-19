<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SongCategory extends Model
{
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'songs_song_categories', 'category_id', 'song_id');
    }
}