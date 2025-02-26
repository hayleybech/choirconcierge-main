<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
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
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Song $song
 *
 * Dynamic
 * @property string $download_url
 * @property string $path
 *
 * Other
 * @property UploadedFile|string $file
 */
class SongAttachment extends Model
{
    use HasFactory, TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'song_id', 'file', 'filepath', 'type'];

    protected $appends = ['download_url'];

    protected $with = ['song'];

    protected $touches = ['song'];

    protected static function booted()
    {
        static::creating(static function (self $song_attachment) {
            $song_attachment->saveFile();
        });
    }

    private UploadedFile|string $file;

    public static function create(array $attributes = [])
    {
        /** @var SongAttachment $attachment */
        $attachment = static::query()->create($attributes);

        return $attachment;
    }

    public function delete()
    {
        if (Storage::disk('public')->exists($this->getPath())) {
            Storage::disk('public')->delete($this->getPath());
        }

        parent::delete();

        return true;
    }

    public function setFileAttribute(UploadedFile|string $file): void
    {
        $this->file = $file;
    }

    public function saveFile(): void
    {
        if($this->type === 'youtube') {
            return;
        }

        Storage::disk('public')->makeDirectory(self::getPathSongs());
        Storage::disk('public')->makeDirectory($this->getPathSong());

        if (Storage::disk('public')->exists($this->getPath())) {
            Storage::disk('public')->delete($this->getPath());
        }

        Storage::disk('public')->putFileAs($this->getPathSong(), $this->file, $this->filepath);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('tenancy.asset', ['path' => $this->getPath(), 'tenant' => $this->song->tenant_id]);
    }

    public function getPathAttribute()
    {
        return Storage::disk('public')->path($this->getPath());
    }

    public static function getPathSongs(): string
    {
        return 'songs';
    }

    public function getPathSong(): string
    {
        // song might be null during seeding
        $song_id = $this->song?->id ?? $this->song_id;
        return $song_id !== null ? self::getPathSongs().'/'.$song_id : '';
    }

    public function getPath(): string
    {
        return $this->getPathSong().'/'.$this->filepath;
    }
}
