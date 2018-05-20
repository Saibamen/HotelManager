<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Room::class, function (Faker $faker) {
    return [
        'number'   => $faker->unique()->numerify(),
        'floor'    => $faker->numberBetween(-2, 50),
        'capacity' => $faker->numberBetween(1, 25),
        'price'    => $faker->randomFloat(),
        'comment'  => 'test comment',
    ];
});
