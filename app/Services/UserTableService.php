<?php

namespace App\Services;

use App\Models\User;

class UserTableService implements TableServiceInterface
{
    public function getRouteName()
    {
        return 'user';
    }

    public function getColumns()
    {
        $dataset = [
            [
                'title' => trans('general.name'),
                'value' => function (User $data) {
                    return $data->name;
                },
            ],
            [
                'title' => trans('auth.email'),
                'value' => function (User $data) {
                    return $data->email;
                },
            ],
            [
                'title' => trans('general.created'),
                'value' => function (User $data) {
                    return $data->created_at;
                },
            ],
        ];

        return $dataset;
    }
}
