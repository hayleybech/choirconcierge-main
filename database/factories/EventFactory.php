<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(\App\Models\Event::class, static function (Faker $faker) {
    $call_time = Carbon::instance( $faker->dateTimeThisYear() );
    $start_time = (clone $call_time)->addHour();
    $end_time = (clone $start_time)->addHours(2);

    return [
        'title' => $faker->sentence(6, true),
        'call_time' => $call_time,
        'start_date' => $start_time,
        'end_date' => $end_time,
        'location_name' => $faker->sentence(3, true),
        'location_address' => $faker->address, // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
        'description' => $faker->optional()->sentence,
    ];
});
