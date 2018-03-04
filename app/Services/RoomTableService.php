<?php

namespace App\Services;

use App\Models\Room;

class RoomTableService implements TableServiceInterface
{
    public function getRouteName()
    {
        return 'room';
    }

    public function getColumns()
    {
        $dataset = [
            [
                'title' => trans('general.number'),
                'value' => function (Room $data) {
                    return $data->number;
                },
            ],
            [
                'title' => trans('general.floor'),
                'value' => function (Room $data) {
                    return $data->floor;
                },
            ],
            [
                'title' => trans('general.capacity'),
                'value' => function (Room $data) {
                    return $data->capacity;
                },
            ],
            [
                'title' => trans('general.price'),
                'value' => function (Room $data) {
                    return $data->price;
                },
            ],
            [
                'title' => trans('general.comment'),
                'value' => function (Room $data) {
                    return $data->comment;
                },
            ],
        ];

        return $dataset;
    }
}
