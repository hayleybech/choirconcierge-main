<?php
use App\Models\SongAttachment;
use Faker\Generator as Faker;

$factory->define(SongAttachment::class, static function (Faker $faker) {

    return [
        'title' => $faker->sentence(2, true),
        'filepath' => '---',    // Set in seeder, this temporarily circumvents non nullable column
    ];
});
