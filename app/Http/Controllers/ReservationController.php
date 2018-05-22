<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\ManageTableInterface;
use App\Http\Requests\ReservationAddRequest;
use App\Http\Requests\ReservationEditRequest;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\GuestTableService;
use App\Services\ReservationTableService;
use App\Services\RoomTableService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReservationController extends Controller implements ManageTableInterface
{
    protected $reservationTableService;

    public function __construct(ReservationTableService $reservationTableService)
    {
        $this->reservationTableService = $reservationTableService;
    }

    public function index()
    {
        $title = trans('navigation.all_reservations');

        $dataset = Reservation::select('id', 'room_id', 'guest_id', 'date_start', 'date_end', 'people')
            ->with('guest:id,first_name,last_name')
            ->with('room:id,number')
            ->orderBy('date_end', 'DESC')
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_reservations_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'       => $this->reservationTableService->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->reservationTableService->getRouteName(),
            'title'         => $title,
        ];

        return view('list', $viewData);
    }

    public function current()
    {
        $title = trans('navigation.current_reservations');

        $dataset = Reservation::select('id', 'room_id', 'guest_id', 'date_start', 'date_end', 'people')
            ->with('guest:id,first_name,last_name')
            ->with('room:id,number')
            ->getCurrentReservations()
            ->orderBy('date_end')
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_reservations_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'       => $this->reservationTableService->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->reservationTableService->getRouteName(),
            'title'         => $title,
        ];

        return view('list', $viewData);
    }

    public function future()
    {
        $title = trans('navigation.future_reservations');

        $dataset = Reservation::select('id', 'room_id', 'guest_id', 'date_start', 'date_end', 'people')
            ->with('guest:id,first_name,last_name')
            ->with('room:id,number')
            ->getFutureReservations()
            ->orderBy('date_end')
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_reservations_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'       => $this->reservationTableService->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->reservationTableService->getRouteName(),
            'title'         => $title,
        ];

        return view('list', $viewData);
    }

    public function chooseGuest(GuestTableService $guestTableService)
    {
        $title = trans('navigation.choose_guest');

        $dataset = Guest::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_guests_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'         => $guestTableService->getColumns(),
            'dataset'         => $dataset,
            'routeName'       => $guestTableService->getRouteName(),
            'title'           => $title,
            'routeChooseName' => $this->reservationTableService->getRouteName().'.search_free_rooms',
        ];

        return view('list', $viewData);
    }

    public function searchFreeRooms($guestId)
    {
        try {
            $guest = Guest::select('id', 'first_name', 'last_name')->findOrFail($guestId);
        } catch (ModelNotFoundException $e) {
            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $dataset = new Reservation();
        $dataset->guest()->associate($guest);
        $title = trans('navigation.search_free_rooms');
        $submitRoute = route($this->reservationTableService->getRouteName().'.post_search_free_rooms', [$dataset->guest->id]);

        $fiels = $this->getFields(true);
        array_unshift($fiels, $this->getGuestField());

        $viewData = [
            'dataset'     => $dataset,
            'fields'      => $fiels,
            'title'       => $title,
            'submitRoute' => $submitRoute,
        ];

        return view('addedit', $viewData);
    }

    public function postSearchFreeRooms(ReservationAddRequest $request, $guestId = null)
    {
        try {
            $guest = Guest::select('id')->findOrFail($guestId);
        } catch (ModelNotFoundException $e) {
            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $data = $request->only(['date_start', 'date_end', 'people']);

        return redirect()->route($this->reservationTableService->getRouteName().'.choose_free_room', [$guest->id])
            ->with($data);
    }

    /**
     * @param RoomTableService $roomTableService
     * @param int              $guestId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function chooseFreeRoom(RoomTableService $roomTableService, $guestId)
    {
        if (!$this->isReservationDataInSessionCorrect()) {
            return $this->returnBack([
                'message'     => trans('general.session_error'),
                'alert-class' => 'alert-danger',
            ]);
        }

        try {
            $guest = Guest::select('id', 'first_name', 'last_name')->findOrFail($guestId);
        } catch (ModelNotFoundException $e) {
            Log::warning(__CLASS__.'::'.__FUNCTION__.' at '.__LINE__.': '.$e->getMessage());

            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $dateStart = Session::get('date_start');
        $dateEnd = Session::get('date_end');
        $people = Session::get('people');

        $dateStart = Carbon::parse($dateStart);
        $dateEnd = Carbon::parse($dateEnd);

        $title = trans('navigation.choose_room_for').' '.$guest->fullName;

        $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price', 'comment')
            ->freeRoomsForReservation($dateStart, $dateEnd, $people)
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_rooms_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'               => $roomTableService->getColumns(),
            'dataset'               => $dataset,
            'routeName'             => $roomTableService->getRouteName(),
            'title'                 => $title,
            'routeChooseName'       => $this->reservationTableService->getRouteName().'.add',
            'additionalRouteParams' => $guest->id,
        ];

        return view('list', $viewData);
    }

    public function add($guestId, $roomId)
    {
        if (!$this->isReservationDataInSessionCorrect()) {
            return $this->returnBack([
                'message'     => trans('general.session_error'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $dateStart = Session::get('date_start');
        $dateEnd = Session::get('date_end');
        $people = Session::get('people');

        $dateStart = Carbon::parse($dateStart);
        $dateEnd = Carbon::parse($dateEnd);

        try {
            $guest = Guest::select('id')->findOrFail($guestId);
            $room = Room::select('id', 'capacity')->findOrFail($roomId);
        } catch (ModelNotFoundException $e) {
            Log::warning(__CLASS__.'::'.__FUNCTION__.' at '.__LINE__.': '.$e->getMessage());

            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        if ($room->capacity < $people) {
            return $this->returnBack([
                'message'     => trans('general.people_exceeds_room_capacity'),
                'alert-class' => 'alert-danger',
            ]);
        }

        if (!$room->isFree($dateStart, $dateEnd)) {
            return $this->returnBack([
                'message'     => trans('general.dates_coincide_different_booking'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $reservation = new Reservation();
        $reservation->guest_id = $guest->id;
        $reservation->room_id = $room->id;
        $reservation->date_start = $dateStart;
        $reservation->date_end = $dateEnd;
        $reservation->people = $people;

        $reservation->save();

        $this->addFlashMessage(trans('general.saved'), 'alert-success');

        return redirect()->route($this->reservationTableService->getRouteName().'.index');
    }

    public function postEdit(ReservationEditRequest $request, $objectId)
    {
        try {
            $object = Reservation::with('room:id,capacity')
                ->findOrFail($objectId);
        } catch (ModelNotFoundException $e) {
            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        // Check room capacity for people in reservation
        if ($object->room->capacity < $request->input('people')) {
            return redirect()->back()->with([
                'message'     => trans('general.people_exceeds_room_capacity'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $dateStart = Carbon::parse($request->input('date_start'));
        $dateEnd = Carbon::parse($request->input('date_end'));

        // Check if dates can be changed
        $reservationsIdsForDates = DB::table('reservations')
            ->where('room_id', $object->room_id)
            ->where('id', '!=', $object->id)
            ->where(function (Builder $query) use ($dateStart, $dateEnd) {
                $query->where(function (Builder $query) use ($dateStart, $dateEnd) {
                    $query->where('date_start', '<', $dateEnd)
                        ->where('date_end', '>', $dateStart);
                });
            })
            ->count();

        if ($reservationsIdsForDates > 0) {
            return redirect()->back()->with([
                'message'     => trans('general.dates_coincide_different_booking'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $object->fill($request->all());
        $object->save();

        return redirect()->route($this->reservationTableService->getRouteName().'.index')
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function delete($objectId)
    {
        try {
            $object = Reservation::findOrFail($objectId);
        } catch (ModelNotFoundException $e) {
            $data = ['class' => 'alert-danger', 'message' => trans('general.object_not_found')];

            return response()->json($data);
        }

        $object->delete();

        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    public function showEditForm($objectId)
    {
        try {
            $dataset = Reservation::select('id', 'room_id', 'guest_id', 'date_start', 'date_end', 'people')
            ->with('guest:id,first_name,last_name')
            ->with('room:id,number')
            ->findOrFail($objectId);
        } catch (ModelNotFoundException $e) {
            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $title = trans('navigation.edit_reservation');
        $submitRoute = route($this->reservationTableService->getRouteName().'.postedit', $objectId);

        $fiels = $this->getFields();
        array_unshift($fiels, $this->getGuestField(), $this->getRoomField(), $this->getActionButtons());

        $viewData = [
            'dataset'     => $dataset,
            'fields'      => $fiels,
            'title'       => $title,
            'submitRoute' => $submitRoute,
            'routeName'   => $this->reservationTableService->getRouteName(),
        ];

        return view('addedit', $viewData);
    }

    public function editChooseGuest(GuestTableService $guestTableService, $reservationId)
    {
        try {
            $reservation = Reservation::select('id', 'guest_id')->findOrFail($reservationId);
        } catch (ModelNotFoundException $e) {
            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $title = trans('navigation.change_guest_for_reservation');

        $dataset = Guest::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')
            ->whereNotIn('id', [$reservation->guest_id])
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_guests_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'               => $guestTableService->getColumns(),
            'dataset'               => $dataset,
            'routeName'             => $guestTableService->getRouteName(),
            'title'                 => $title,
            'routeChooseName'       => $this->reservationTableService->getRouteName().'.edit_change_guest',
            'additionalRouteParams' => $reservation->id,
        ];

        return view('list', $viewData);
    }

    public function editChangeGuest($reservationId, $guestId)
    {
        try {
            $reservation = Reservation::select('id')->findOrFail($reservationId);
            $guest = Guest::select('id')->findOrFail($guestId);
        } catch (ModelNotFoundException $e) {
            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $reservation->guest_id = $guest->id;
        $reservation->save();

        return redirect()->route($this->reservationTableService->getRouteName().'.editform', [$reservation->id])
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function editChooseRoom(RoomTableService $roomTableService, $reservationId)
    {
        try {
            $reservation = Reservation::select('id', 'guest_id', 'date_start', 'date_end', 'people')
                ->findOrFail($reservationId);
        } catch (ModelNotFoundException $e) {
            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $dateStart = Carbon::parse($reservation->date_start);
        $dateEnd = Carbon::parse($reservation->date_end);

        $title = trans('navigation.change_room_for_reservation');

        $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price', 'comment')
            ->freeRoomsForReservation($dateStart, $dateEnd, $reservation->people)
            ->paginate($this->getItemsPerPage());

        if ($dataset->isEmpty()) {
            $this->addFlashMessage(trans('general.no_rooms_in_database'), 'alert-danger');
        }

        $viewData = [
            'columns'               => $roomTableService->getColumns(),
            'dataset'               => $dataset,
            'routeName'             => $roomTableService->getRouteName(),
            'title'                 => $title,
            'routeChooseName'       => $this->reservationTableService->getRouteName().'.edit_change_room',
            'additionalRouteParams' => $reservation->id,
        ];

        return view('list', $viewData);
    }

    public function editChangeRoom($reservationId, $roomId)
    {
        try {
            $reservation = Reservation::select('id', 'people', 'date_start', 'date_end')
                ->findOrFail($reservationId);
            $room = Room::select('id', 'capacity')->findOrFail($roomId);
        } catch (ModelNotFoundException $e) {
            return $this->returnBack([
                'message'     => trans('general.object_not_found'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $dateStart = Carbon::parse($reservation->date_start);
        $dateEnd = Carbon::parse($reservation->date_end);

        if ($room->capacity < $reservation->people) {
            return $this->returnBack([
                'message'     => trans('general.people_exceeds_room_capacity'),
                'alert-class' => 'alert-danger',
            ]);
        }

        if (!$room->isFree($dateStart, $dateEnd)) {
            return $this->returnBack([
                'message'     => trans('general.dates_coincide_different_booking'),
                'alert-class' => 'alert-danger',
            ]);
        }

        $reservation->room_id = $room->id;
        $reservation->save();

        return redirect()->route($this->reservationTableService->getRouteName().'.editform', [$reservation->id])
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function getGuestField()
    {
        return [
            'id'    => 'guest',
            'title' => trans('general.guest'),
            'value' => function (Reservation $data) {
                return $data->guest->full_name;
            },
            'optional' => [
                'readonly' => 'readonly',
            ],
        ];
    }

    public function getRoomField()
    {
        return [
            'id'    => 'room',
            'title' => trans('general.room'),
            'value' => function (Reservation $data) {
                return $data->room->number;
            },
            'optional' => [
                'readonly' => 'readonly',
            ],
        ];
    }

    public function getActionButtons()
    {
        return [
            'id'         => 'action_buttons',
            'type'       => 'buttons',
            'buttons'    => [
                [
                    'value' => function () {
                        return trans('general.change_guest');
                    },
                    'route_name'  => 'reservation.edit_choose_guest',
                    'route_param' => function (Reservation $data) {
                        return $data->id;
                    },
                    'optional' => [
                        'class' => 'btn btn-primary',
                    ],
                ],
                [
                    'value' => function () {
                        return trans('general.change_room');
                    },
                    'route_name'  => 'reservation.edit_choose_room',
                    'route_param' => function (Reservation $data) {
                        return $data->id;
                    },
                    'optional' => [
                        'class' => 'btn btn-primary',
                    ],
                ],
            ],
        ];
    }

    public function getFields($forAdd = false)
    {
        return [
            [
                'id'    => 'date_start',
                'title' => trans('general.date_start'),
                'value' => function (Reservation $data) {
                    return $data->date_start;
                },
                'type'     => 'text',
                'optional' => [
                    'required'    => 'required',
                    'class'       => 'datepicker'.($forAdd ? ' start-date' : null),
                    'placeholder' => 'dd.mm.rrrr',
                ],
            ],
            [
                'id'    => 'date_end',
                'title' => trans('general.date_end'),
                'value' => function (Reservation $data) {
                    return $data->date_end;
                },
                'type'     => 'text',
                'optional' => [
                    'required'    => 'required',
                    'class'       => 'datepicker'.($forAdd ? ' end-date' : null),
                    'placeholder' => 'dd.mm.rrrr',
                ],
            ],
            [
                'id'    => 'people',
                'title' => trans('general.number_of_people'),
                'value' => function (Reservation $data) {
                    return $data->people ?: 1;
                },
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                    'min'      => '1',
                ],
            ],
        ];
    }

    private function isReservationDataInSessionCorrect()
    {
        if (!Session::has(['date_start', 'date_end', 'people'])) {
            Log::error('Missing one of Session keys: date_start, date_end, people');

            return false;
        }

        Session::reflash();

        return true;
    }
}
