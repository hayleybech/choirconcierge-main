<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class SongCategory
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Song[] $songs
 *
 * @package App\Models
 */
class SongCategory extends Model
{
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'songs_song_categories', 'category_id', 'song_id');
    }
}