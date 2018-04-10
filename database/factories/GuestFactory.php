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

$factory->state(App\Models\Guest::class, 'polish', function () {
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

$factory->state(App\Models\Guest::class, 'german', function () {
    $faker = \Faker\Factory::create('de_DE');

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

$factory->state(App\Models\Guest::class, 'french', function () {
    $faker = \Faker\Factory::create('fr_FR');

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

$factory->state(App\Models\Guest::class, 'dutch', function () {
    $faker = \Faker\Factory::create('nl_NL');

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
