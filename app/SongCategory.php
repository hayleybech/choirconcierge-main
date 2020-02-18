<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SongCategory extends Model
{
    public function songs()
    {
        return $this->belongsToMany('App\Song', 'songs_song_categories', 'category_id', 'song_id');
    }
}