<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Reservation::class, function (Faker $faker) use ($factory) {
    return [
        'room_id'     => function ($post) use ($factory, $faker) {
            return $factory->create(App\Models\Room::class, [
                'capacity' => $faker->numberBetween(isset($post['people']) ? (int) $post['people'] : 1, 99),
            ])->id;
        },
        'guest_id'    => $factory->create(App\Models\Guest::class)->id,
        'date_start'  => $faker->date(),
        'date_end'    => $faker->dateTimeBetween('tomorrow', '120 days')->format('Y-m-d'),
        'people'      => $faker->numberBetween(1, 99),
    ];
});
