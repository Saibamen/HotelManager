<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Reservation::class, function (Faker $faker) use ($factory)  {
    return [
        'room_id'     => $factory->create(App\Models\Room::class)->id,
        'guest_id'    => $factory->create(App\Models\Guest::class)->id,
        'date_start'  => $faker->date(),
        'date_end'    => $faker->date(),
        'people'      => $faker->numberBetween(1, 99),
    ];
});
