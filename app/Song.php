<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Song extends Model
{
    public function status(): BelongsTo
    {
        return $this->belongsTo('App\SongStatus', 'status_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany('App\SongCategory', 'songs_song_categories', 'song_id', 'category_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany('App\SongAttachment');
    }

    public function getPitchBlown(): string
    {
        return self::getAllPitches()[$this->pitch_blown];
    }

    public static function getAllPitchesByMode(): array
    {
        return [
            'Major' => [
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
            ],
            'Minor' => [
                12  => 'Am',
                13  => 'A#m/Bbm',
                14  => 'Bm',
                15  => 'Cm',
                16  => 'C#m/Dbm',
                17  => 'Dm',
                18  => 'D#m/Ebm',
                19  => 'Em',
                20  => 'Fm',
                21  => 'F#m/Gbm',
                22  => 'Gm',
                23  => 'G#m/Abm',
            ],
        ];
    }
    public static function getAllPitches(): array
    {
        $all_pitches = [];
        foreach( self::getAllPitchesByMode() as $mode => $pitches ) {
            $all_pitches = array_merge($all_pitches, $pitches);
        }
        return $all_pitches;
    }
}
