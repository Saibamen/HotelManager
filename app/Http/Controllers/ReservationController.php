<?php

namespace App\Http\Controllers;

// TODO
use App\Http\Requests\GuestRequest;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;

class ReservationController extends Controller
{
    private function getRouteName()
    {
        return 'reservation';
    }

    public function index()
    {
        $title = trans('general.reservations');

        $dataset = Reservation::select('id', 'room_id', 'guest_id', 'date_start', 'date_end', 'people')
            ->with('guest:id,first_name,last_name')
            ->with('room:id,number')
            ->paginate($this->getItemsPerPage());

        $viewData = [
            'columns'       => $this->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->getRouteName(),
            'title'         => $title,
            // TODO
            'deleteMessage' => mb_strtolower(trans('general.reservation')).' '.mb_strtolower(trans('general.number')),
        ];

        if ($dataset->isEmpty()) {
            Session::flash('message', trans('general.no_reservations_in_database'));
            Session::flash('alert-class', 'alert-danger');
        }

        return view('list', $viewData);
    }

    // TODO
    public function store(GuestRequest $request, $id = null)
    {
        if ($id === null) {
            $object = new Reservation();
        } else {
            try {
                $object = Reservation::findOrFail($id);
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
        Reservation::destroy($id);
        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    // TODO
    public function showAddEditForm($id = null)
    {
        if ($id === null) {
            $dataset = new Reservation();
            $title = trans('navigation.add_guest');
            $submitRoute = route($this->getRouteName().'.postadd');
        } else {
            try {
                $dataset = Reservation::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return Controller::returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }

            $title = trans('navigation.edit_guest');
            $submitRoute = route($this->getRouteName().'.postedit', $id);
        }

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
                'value' => function (Reservation $data) {
                    return $data->first_name;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'last_name',
                'title' => trans('general.last_name'),
                'value' => function (Reservation $data) {
                    return $data->last_name;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'address',
                'title' => trans('general.address'),
                'value' => function (Reservation $data) {
                    return $data->address;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'zip_code',
                'title' => trans('general.zip_code'),
                'value' => function (Reservation $data) {
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
                'value' => function (Reservation $data) {
                    return $data->place;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'PESEL',
                'title' => trans('general.PESEL'),
                'value' => function (Reservation $data) {
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
                'value' => function (Reservation $data) {
                    return $data->contact;
                },
                'type'     => 'textarea',
                'optional' => [
                    'placeholder' => trans('general.contact_placeholder'),
                ],
            ],
        ];
    }

    // TODO
    private function getColumns()
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
