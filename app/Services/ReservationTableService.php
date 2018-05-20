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
                    return '<a href="'.route('room.editform', [$data->room->id]).'">'.$data->room->number.'</a>';
                },
            ],
            [
                'title' => trans('general.guest'),
                'value' => function (Reservation $data) {
                    return '<a href="'.route('guest.editform', [$data->guest->id]).'">'.$data->guest->full_name.'</a>';
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
                'title' => trans('general.number_of_people'),
                'value' => function (Reservation $data) {
                    return $data->people;
                },
            ],
        ];

        return $dataset;
    }
}
