<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Guest::class, function (Faker $faker) {
    return [
        'first_name'   => $faker->firstName(),
        'last_name'    => $faker->lastName(),
        'address' => $faker->streetAddress(),
        'zip_code'    => $faker->numerify('##-###'),
        'place'  => $faker->city(),
        'PESEL'  => $faker->numerify('###########'),
        'contact'  => 'test contact',
    ];
});
