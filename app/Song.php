<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    public function status()
    {
        return $this->belongsTo('App\SongStatus', 'status_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\SongCategory', 'songs_song_categories', 'category_id', 'song_id');
    }
}
