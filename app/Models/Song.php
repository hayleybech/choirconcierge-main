<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Song
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property string $pitch_blown
 * @property int $status_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property SongStatus $status
 * @property SongCategory[] $categories
 * @property SongAttachment[] $attachments
 *
 * Dynamic
 * @property string $pitch
 *
 * @package App\Models
 */
class Song extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'pitch_blown',
    ];

    public const PITCHES = [
        0   => 'A',
        1   => 'A#/Bb',
        2   => 'B',
        3   => 'C',
        4   => 'C#/Db',
        5   => 'D',
        6   => 'D#/Eb',
        7   => 'E',
        8   => 'F',
        9   => 'F#/Gb',
        10  => 'G',
        11  => 'G#/Ab',
    ];
    public const KEYS = [
        'Major' => self::PITCHES,
        'Minor' => self::PITCHES,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['pitch'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(SongStatus::class, 'status_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(SongCategory::class, 'songs_song_categories', 'song_id', 'category_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SongAttachment::class);
    }

    public function getPitchAttribute(): string
    {
        return self::getAllPitches()[$this->pitch_blown];
    }

    public static function getAllPitches(): array
    {
        $all_pitches = [];
        foreach( self::KEYS as $mode => $pitches ) {
            $all_pitches = array_merge($all_pitches, $pitches);
        }
        return $all_pitches;
    }
}
