<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\UploadedFile;
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
 * @property string $path
 *
 * @package App\Models
 */
class SongAttachment extends Model
{
    use HasFactory, TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'category_id',
        'song_id',
        'file',
    ];

    protected $appends = [
        'download_url',
    ];

    protected $with = [
        'song',
    ];

    protected $touches = ['song'];

    public static function create( array $attributes = [] )
    {
        /** @var SongAttachment $attachment */
        $attachment = static::query()->create($attributes);

        return $attachment;
    }

    public function delete() {
        if (Storage::disk('public')->exists( $this->getPath() )) {
            Storage::disk('public')->delete( $this->getPath() );
        }

        parent::delete();
        return true;
    }

    public function setFileAttribute(UploadedFile $file) {
        $this->filepath = $file->getClientOriginalName();

        Storage::disk('public')->makeDirectory( SongAttachment::getPathSongs() );
        Storage::disk('public')->makeDirectory( $this->getPathSong() );

        if (Storage::disk('public')->exists( $this->getPath() )) {
            Storage::disk('public')->delete( $this->getPath() );
        }

        Storage::disk('public')->putFileAs( $this->getPathSong(), $file, $this->filepath );
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
        return asset($this->getPath());
    }
    public function getPathAttribute() {
        return storage_path('app/public/' . $this->getPath());
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
