<?php

namespace App\Http\Controllers;

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
}
