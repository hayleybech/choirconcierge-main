<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SongAttachment extends Model
{
    public function song() {
        return $this->belongsTo('App\Song');
    }

    public function category() {
        return $this->belongsTo('App\SongAttachmentCategory', 'category_id');
    }

    public function getDownloadUrl() {
        return Storage::url($this->getPath());
    }

    public static function getPathSongs() {
        return 'songs';
    }

    public function getPathSong() {
        return self::getPathSongs() . '/' . $this->song->id;
    }

    public function getPath() {
        return $this->getPathSong() . '/' . $this->filepath;
    }
}
