<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Room::class, function (Faker $faker) {
    return [
        'number'   => $faker->numerify(),
        'floor'    => $faker->numberBetween(-2, 50),
        'capacity' => $faker->numberBetween(1, 25),
        'price'    => $faker->numerify(),
        'comment'  => 'test comment',
    ];
});
