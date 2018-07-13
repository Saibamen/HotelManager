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
        'zip_code'   => $faker->postcode(),
        'place'      => $faker->city(),
        'PESEL'      => $faker->pesel(),
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

$factory->state(App\Models\Guest::class, 'belarus', function () {
    $faker = \Faker\Factory::create('bg_BG');

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

$factory->state(App\Models\Guest::class, 'czech', function () {
    $faker = \Faker\Factory::create('cs_CZ');

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
