<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitialStateRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function getRouteName()
    {
        return 'admin';
    }

    public function index()
    {
        $title = trans('general.administration_panel');

        $viewData = [
            'routeName'     => $this->getRouteName(),
            'title'         => $title,
        ];

        return view('admin', $viewData);
    }

    public function showInitialStateForm()
    {
        $title = trans('navigation.generate_initial_state');

        $fiels = $this->getInitialStateFields();

        $viewData = [
            'dataset'     => null,
            'fields'      => $fiels,
            'title'       => $title,
            'submitRoute' => route($this->getRouteName().'.postgenerate'),
            'routeName'   => $this->getRouteName(),
        ];

        return view('addedit', $viewData);
    }

    public function postInitialState(InitialStateRequest $request)
    {
        factory(\App\Models\Room::class, (int) $request->input('rooms'))->create();

        if (App::isLocale('pl')) {
            factory(\App\Models\Guest::class, (int) $request->input('guests'))->states('polish')->create();
        } else {
            factory(\App\Models\Guest::class, (int) $request->input('guests'))->create();
        }

        return redirect()->route($this->getRouteName().'.index')
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function deleteAllRooms()
    {
        DB::table('reservations')->delete();
        DB::table('rooms')->delete();

        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    public function deleteAllGuests()
    {
        DB::table('reservations')->delete();
        DB::table('guests')->delete();

        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    public function deleteAllReservations()
    {
        DB::table('reservations')->delete();

        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    public function getInitialStateFields()
    {
        return [
            [
                'id'    => 'rooms',
                'title' => trans('general.rooms'),
                'value' => function () {
                    return 1;
                },
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                    'min'      => '1',
                ],
            ],
            [
                'id'    => 'guests',
                'title' => trans('general.guests'),
                'value' => function () {
                    return 1;
                },
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                    'min'      => '1',
                ],
            ],
        ];
    }
}
