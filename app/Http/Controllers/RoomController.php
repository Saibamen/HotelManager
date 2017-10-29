<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoomController extends Controller
{
    private function getRouteName()
    {
        return 'room';
    }

    public function index()
    {
        $title = trans('general.rooms');

        $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price', 'comment')
            ->paginate($this->getItemsPerPage());

        $viewData = [
            'columns'       => $this->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->getRouteName(),
            'title'         => $title,
            'deleteMessage' => mb_strtolower(trans('general.room')).' '.mb_strtolower(trans('general.number')),
        ];

        return view('list', $viewData);
    }

    public function store(RoomRequest $request, $id = null)
    {
        if ($id === null) {
            $object = new Room();
        } else {
            try {
                $object = Room::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return Controller::returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }
        }

        $object->fill($request->all());
        $object->save();

        return redirect()->route($this->getRouteName().'.index')
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function delete($id)
    {
        Room::destroy($id);
        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    public function showAddEditForm($id = null)
    {
        if ($id === null) {
            $dataset = new Room();
            $title = trans('general.add');
            $submitRoute = route($this->getRouteName().'.postadd');
        } else {
            try {
                $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price', 'comment')->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return Controller::returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }

            $title = trans('general.edit');
            $submitRoute = route($this->getRouteName().'.postedit', $id);
        }

        $title .= ' '.mb_strtolower(trans('general.room'));

        $viewData = [
            'dataset'     => $dataset,
            'fields'      => $this->getFields(),
            'title'       => $title,
            'submitRoute' => $submitRoute,
            'routeName'   => $this->getRouteName(),
        ];

        return view('addedit', $viewData);
    }

    private function getFields()
    {
        return [
            [
                'id'    => 'number',
                'title' => trans('general.number'),
                'value' => function (Room $data) {
                    return $data->number;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'floor',
                'title' => trans('general.floor'),
                'value' => function (Room $data) {
                    return $data->floor;
                },
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'capacity',
                'title' => trans('general.capacity'),
                'value' => function (Room $data) {
                    return $data->capacity;
                },
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'price',
                'title' => trans('general.price'),
                'value' => function (Room $data) {
                    return $data->price;
                },
                'type'     => 'number',
                'optional' => [
                    'step'        => '0.01',
                    'placeholder' => '0.00',
                ],
            ],
            [
                'id'    => 'comment',
                'title' => trans('general.comment'),
                'value' => function (Room $data) {
                    return $data->comment;
                },
                'type' => 'textarea',
            ],
        ];
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
