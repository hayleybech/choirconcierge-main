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

    public function getPitchBlown()
    {
        return self::getAllPitches()[$this->pitch_blown];
    }

    public static function getAllPitchesByMode() {
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
    public static function getAllPitches() {
        $all_pitches = [];
        foreach( self::getAllPitchesByMode() as $mode => $pitches ) {
            $all_pitches = array_merge($all_pitches, $pitches);
        }
        return $all_pitches;
    }
}
