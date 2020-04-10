<?php

use App\Models\Song;
use Faker\Generator as Faker;

$factory->define(Song::class, static function (Faker $faker) {
    $pitch = array_rand( Song::getAllPitches() );

    return [
        'title' => $faker->sentence(6, true),
        'pitch_blown' => $pitch,
    ];
});
