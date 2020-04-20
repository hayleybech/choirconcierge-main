<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class SongAttachment
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property string $filepath
 * @property int $song_id
 * @property int $category_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Song $song
 * @property SongAttachmentCategory $category
 *
 * Dynamic
 * @property string $download_url
 *
 * @package App\Models
 */
class SongAttachment extends Model
{
    protected $appends = [
        'download_url',
    ];

    public function delete() {
        if (Storage::disk('public')->exists( $this->getPath() )) {
            Storage::disk('public')->delete( $this->getPath() );
        }

        parent::delete();
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SongAttachmentCategory::class, 'category_id');
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
