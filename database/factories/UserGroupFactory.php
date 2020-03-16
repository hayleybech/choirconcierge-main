<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserGroup;
use Faker\Generator as Faker;

$factory->define(UserGroup::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'list_type' => 'chat'
    ];
});
