<?php

namespace App\Http\Controllers;

use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $title = trans('general.rooms');

        $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price')
            ->paginate(10);

        $view_data = [
            'dataset' => $dataset,
            'columns' => $this->getColumns(),
            'title'   => $title,
        ];

        return view('list', $view_data);
    }

    private function getColumns()
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
        ];

        return $dataset;
    }
}
