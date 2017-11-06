<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestRequest;
use App\Models\Guest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GuestController extends Controller
{
    private function getRouteName()
    {
        return 'guest';
    }

    // TODO
    public function index()
    {
        $title = trans('general.guests');

        $dataset = Guest::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')
            ->paginate($this->getItemsPerPage());

        $viewData = [
            'columns'       => $this->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->getRouteName(),
            'title'         => $title,
            'deleteMessage' => mb_strtolower(trans('general.guest')).' '.mb_strtolower(trans('general.number')),
        ];

        return view('list', $viewData);
    }

    // TODO
    public function store(GuestRequest $request, $id = null)
    {
        if ($id === null) {
            $object = new Guest();
        } else {
            try {
                $object = Guest::findOrFail($id);
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
        Guest::destroy($id);
        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    // TODO
    public function showAddEditForm($id = null)
    {
        if ($id === null) {
            $dataset = new Guest();
            $title = trans('general.add');
            $submitRoute = route($this->getRouteName().'.postadd');
        } else {
            try {
                $dataset = Guest::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return Controller::returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }

            $title = trans('general.edit');
            $submitRoute = route($this->getRouteName().'.postedit', $id);
        }

        $title .= ' '.mb_strtolower(trans('general.guest'));

        $viewData = [
            'dataset'     => $dataset,
            'fields'      => $this->getFields(),
            'title'       => $title,
            'submitRoute' => $submitRoute,
            'routeName'   => $this->getRouteName(),
        ];

        return view('addedit', $viewData);
    }

    // TODO
    private function getFields()
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
                'type'     => 'number',
                'optional' => [
                    'required'    => 'required',
                    'step'        => '0.01',
                    'placeholder' => '0.00',
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
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'contact',
                'title' => trans('general.contact'),
                'value' => function (Guest $data) {
                    return $data->contact;
                },
                'type' => 'textarea',
            ],
        ];
    }

    // TODO
    private function getColumns()
    {
        $dataset = [
            [
                'title' => trans('general.first_name'),
                'value' => function (Guest $data) {
                    return $data->first_name;
                },
            ],
            [
                'title' => trans('general.last_name'),
                'value' => function (Guest $data) {
                    return $data->last_name;
                },
            ],
            [
                'title' => trans('general.address'),
                'value' => function (Guest $data) {
                    return $data->address;
                },
            ],
            [
                'title' => trans('general.zip_code'),
                'value' => function (Guest $data) {
                    return $data->zip_code;
                },
            ],
            [
                'title' => trans('general.place'),
                'value' => function (Guest $data) {
                    return $data->place;
                },
            ],
            [
                'title' => trans('general.PESEL'),
                'value' => function (Guest $data) {
                    return $data->PESEL;
                },
            ],
            [
                'title' => trans('general.contact'),
                'value' => function (Guest $data) {
                    return $data->contact;
                },
            ],
        ];

        return $dataset;
    }
}
