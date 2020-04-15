<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Report;
use Faker\Generator as Faker;

$factory->define(Report::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'date' => $faker->dateTimeBetween('-2 year', 'now'),
        'type' => $faker->randomElement(['medical_board', 'fluorography']),
    ];
});
