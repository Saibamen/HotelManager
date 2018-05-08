<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\ManageTableInterface;
use App\Http\Requests\GuestRequest;
use App\Models\Guest;
use App\Services\GuestTableService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GuestController extends Controller implements ManageTableInterface
{
    protected $guestTableService;

    public function __construct(GuestTableService $guestTableService)
    {
        $this->guestTableService = $guestTableService;
    }

    public function index()
    {
        $title = trans('general.guests');

        $dataset = Guest::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_guests_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'       => $this->guestTableService->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->guestTableService->getRouteName(),
            'title'         => $title,
            'deleteMessage' => trans('general.delete_associated_reservations'),
        ];

        return view('list', $viewData);
    }

    public function store(GuestRequest $request, $objectId = null)
    {
        if ($objectId === null) {
            $object = new Guest();
        } else {
            try {
                $object = Guest::findOrFail($objectId);
            } catch (ModelNotFoundException $e) {
                return $this->returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }
        }

        $object->fill($request->all());
        $object->save();

        return redirect()->route($this->guestTableService->getRouteName().'.index')
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function delete($objectId)
    {
        try {
            $object = Guest::findOrFail($objectId);
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
            $dataset = new Guest();
            $title = trans('navigation.add_guest');
            $submitRoute = route($this->guestTableService->getRouteName().'.postadd');
        } else {
            try {
                $dataset = Guest::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')->findOrFail($objectId);
            } catch (ModelNotFoundException $e) {
                return $this->returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }

            $title = trans('navigation.edit_guest');
            $submitRoute = route($this->guestTableService->getRouteName().'.postedit', $objectId);
        }

        $viewData = [
            'dataset'     => $dataset,
            'fields'      => $this->getFields(),
            'title'       => $title,
            'submitRoute' => $submitRoute,
            'routeName'   => $this->guestTableService->getRouteName(),
        ];

        return view('addedit', $viewData);
    }

    public function getFields()
    {
        return [
            [
                'id'    => 'first_name',
                'title' => trans('general.first_name'),
                'value' => function (Guest $data) {
                    return $data->first_name;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'last_name',
                'title' => trans('general.last_name'),
                'value' => function (Guest $data) {
                    return $data->last_name;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'address',
                'title' => trans('general.address'),
                'value' => function (Guest $data) {
                    return $data->address;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'zip_code',
                'title' => trans('general.zip_code'),
                'value' => function (Guest $data) {
                    return $data->zip_code;
                },
                'optional' => [
                    'required'    => 'required',
                    'placeholder' => '00-000',
                ],
            ],
            [
                'id'    => 'place',
                'title' => trans('general.place'),
                'value' => function (Guest $data) {
                    return $data->place;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'PESEL',
                'title' => trans('general.PESEL'),
                'value' => function (Guest $data) {
                    return $data->PESEL;
                },
                'optional' => [
                    'required'    => 'required',
                    'placeholder' => '12345654321',
                ],
            ],
            [
                'id'    => 'contact',
                'title' => trans('general.contact'),
                'value' => function (Guest $data) {
                    return $data->contact;
                },
                'type'     => 'textarea',
                'optional' => [
                    'placeholder' => trans('general.contact_placeholder'),
                ],
            ],
        ];
    }
}
