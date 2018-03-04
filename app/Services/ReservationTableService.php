<?php

namespace App\Services;

use App\Models\Reservation;

class ReservationTableService implements TableServiceInterface
{
    public function getRouteName()
    {
        return 'reservation';
    }

    public function getColumns()
    {
        $dataset = [
            [
                'title' => trans('general.room'),
                'value' => function (Reservation $data) {
                    return $data->room->number;
                },
            ],
            [
                'title' => trans('general.guest'),
                'value' => function (Reservation $data) {
                    return $data->guest->first_name.' '.$data->guest->last_name;
                },
            ],
            [
                'title' => trans('general.date_start'),
                'value' => function (Reservation $data) {
                    return $data->date_start;
                },
            ],
            [
                'title' => trans('general.date_end'),
                'value' => function (Reservation $data) {
                    return $data->date_end;
                },
            ],
            [
                'title' => trans('general.people'),
                'value' => function (Reservation $data) {
                    return $data->people;
                },
            ],
        ];

        return $dataset;
    }
}
