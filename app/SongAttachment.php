<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SongAttachment extends Model
{
    protected $appends = [
        'download_url',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo('App\Song');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo('App\SongAttachmentCategory', 'category_id');
    }

    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->getPath());
    }

    public static function getPathSongs(): string
    {
        return 'songs';
    }

    public function getPathSong(): string
    {
        return self::getPathSongs() . '/' . $this->song->id;
    }

    public function getPath(): string
    {
        return $this->getPathSong() . '/' . $this->filepath;
    }
}
