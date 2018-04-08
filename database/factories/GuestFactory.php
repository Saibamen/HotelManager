<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Guest::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName(),
        'last_name'  => $faker->lastName(),
        'address'    => $faker->streetAddress(),
        'zip_code'   => $faker->numerify('##-###'),
        'place'      => $faker->city(),
        'PESEL'      => $faker->numerify('###########'),
        'contact'    => 'test contact',
    ];
});

$factory->state(App\Models\Guest::class, 'polish', function (Faker $faker) {
    $faker = \Faker\Factory::create('pl_PL');

    return [
        'first_name' => $faker->firstName(),
        'last_name'  => $faker->lastName(),
        'address'    => $faker->streetAddress(),
        'zip_code'   => $faker->numerify('##-###'),
        'place'      => $faker->city(),
        'PESEL'      => $faker->numerify('###########'),
        'contact'    => 'test contact',
    ];
});
