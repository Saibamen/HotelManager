<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\ManageTableInterface;
use App\Http\Interfaces\TableInterface;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoomController extends Controller implements TableInterface, ManageTableInterface
{
    public function getRouteName()
    {
        return 'room';
    }

    public function index()
    {
        $title = trans('general.rooms');

        $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price', 'comment')
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_rooms_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'       => $this->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->getRouteName(),
            'title'         => $title,
            'deleteMessage' => mb_strtolower(trans('general.room')).' '.mb_strtolower(trans('general.number')),
        ];

        return view('list', $viewData);
    }

    public function store(RoomRequest $request, $objectId = null)
    {
        if ($objectId === null) {
            $object = new Room();
        } else {
            try {
                $object = Room::findOrFail($objectId);
            } catch (ModelNotFoundException $e) {
                return $this->returnBack([
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

    public function delete($objectId)
    {
        Room::destroy($objectId);
        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    public function showAddEditForm($objectId = null)
    {
        if ($objectId === null) {
            $dataset = new Room();
            $title = trans('general.add');
            $submitRoute = route($this->getRouteName().'.postadd');
        } else {
            try {
                $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price', 'comment')->findOrFail($objectId);
            } catch (ModelNotFoundException $e) {
                return $this->returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }

            $title = trans('general.edit');
            $submitRoute = route($this->getRouteName().'.postedit', $objectId);
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

    public function getFields()
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
