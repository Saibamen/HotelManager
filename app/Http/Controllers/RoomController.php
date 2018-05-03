<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\ManageTableInterface;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use App\Services\RoomTableService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoomController extends Controller implements ManageTableInterface
{
    protected $roomTableService;

    public function __construct(RoomTableService $roomTableService)
    {
        $this->roomTableService = $roomTableService;
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
            'columns'       => $this->roomTableService->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->roomTableService->getRouteName(),
            'title'         => $title,
            'deleteMessage' => trans('general.delete_associated_reservations'),
        ];

        return view('list', $viewData);
    }

    public function free()
    {
        $title = trans('navigation.currently_free_rooms');

        $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price', 'comment')
            ->currentlyFreeRooms()
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_rooms_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'       => $this->roomTableService->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->roomTableService->getRouteName(),
            'title'         => $title,
            'deleteMessage' => trans('general.delete_associated_reservations'),
        ];

        return view('list', $viewData);
    }

    public function occupied()
    {
        $title = trans('navigation.currently_occupied_rooms');

        $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price', 'comment')
            ->currentlyOccupiedRooms()
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_rooms_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'       => $this->roomTableService->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->roomTableService->getRouteName(),
            'title'         => $title,
            'deleteMessage' => trans('general.delete_associated_reservations'),
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

        return redirect()->route($this->roomTableService->getRouteName().'.index')
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function delete($objectId)
    {
        try {
            $object = Room::findOrFail($objectId);
        } catch (ModelNotFoundException $e) {
            $data = ['class' => 'alert-danger', 'message' => trans('general.object_not_found')];

            return response()->json($data);
        }

        $object->reservations()->delete();
        $object->delete();

        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    public function showAddEditForm($objectId = null)
    {
        if ($objectId === null) {
            $dataset = new Room();
            $title = trans('general.add');
            $submitRoute = route($this->roomTableService->getRouteName().'.postadd');
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
            $submitRoute = route($this->roomTableService->getRouteName().'.postedit', $objectId);
        }

        $title .= ' '.mb_strtolower(trans('general.room'));

        $viewData = [
            'dataset'     => $dataset,
            'fields'      => $this->getFields(),
            'title'       => $title,
            'submitRoute' => $submitRoute,
            'routeName'   => $this->roomTableService->getRouteName(),
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
                    'min'      => '1',
                ],
            ],
            [
                'id'    => 'price',
                'title' => trans('general.price'),
                'value' => function (Room $data) {
                    return $data->price;
                },
                'optional' => [
                    'required'    => 'required',
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
}
