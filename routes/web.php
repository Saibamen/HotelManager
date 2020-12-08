<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// From laravel\framework\src\Illuminate\Routing\Router.php

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::get('/', function () {
    return redirect()->route('room.index');
})->name('home');

Route::get('lang/{language}', 'Controller@changeLanguage')->name('lang.set');

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'admin'], function () {
        Route::get('admin', 'AdminController@index')->name('admin.index');
        Route::delete('admin/delete_rooms', 'AdminController@deleteAllRooms')->name('admin.delete_rooms');
        Route::delete('admin/delete_guests', 'AdminController@deleteAllGuests')->name('admin.delete_guests');
        Route::delete('admin/delete_reservations', 'AdminController@deleteAllReservations')->name('admin.delete_reservations');
        Route::get('admin/generate_initial_state', 'AdminController@showInitialStateForm')->name('admin.generate');
        Route::post('admin/generate_initial_state', 'AdminController@postInitialState')->name('admin.postgenerate');

        Route::get('user', 'UserController@index')->name('user.index');
        Route::get('user/add', 'UserController@showAddForm')->name('user.addform');
        Route::post('user/add', 'UserController@postAdd')->name('user.postadd');
        Route::delete('user/delete/{id}', 'UserController@delete')->name('user.delete')->where(['id' => '[0-9]+']);
    });

    Route::get('room', 'RoomController@index')->name('room.index');
    Route::get('room/free', 'RoomController@free')->name('room.free');
    Route::get('room/occupied', 'RoomController@occupied')->name('room.occupied');
    Route::get('room/add', 'RoomController@showAddEditForm')->name('room.addform');
    Route::post('room/add', 'RoomController@store')->name('room.postadd');
    Route::get('room/edit/{id}', 'RoomController@showAddEditForm')->name('room.editform')->where(['id' => '[0-9]+']);
    Route::post('room/edit/{id}', 'RoomController@store')->name('room.postedit')->where(['id' => '[0-9]+']);
    Route::delete('room/delete/{id}', 'RoomController@delete')->name('room.delete')->where(['id' => '[0-9]+']);

    Route::get('guest', 'GuestController@index')->name('guest.index');
    Route::get('guest/add', 'GuestController@showAddEditForm')->name('guest.addform');
    Route::post('guest/add', 'GuestController@store')->name('guest.postadd');
    Route::get('guest/edit/{id}', 'GuestController@showAddEditForm')->name('guest.editform')->where(['id' => '[0-9]+']);
    Route::post('guest/edit/{id}', 'GuestController@store')->name('guest.postedit')->where(['id' => '[0-9]+']);
    Route::delete('guest/delete/{id}', 'GuestController@delete')->name('guest.delete')->where(['id' => '[0-9]+']);

    Route::get('reservation', 'ReservationController@index')->name('reservation.index');
    Route::get('reservation/current', 'ReservationController@current')->name('reservation.current');
    Route::get('reservation/future', 'ReservationController@future')->name('reservation.future');
    Route::get('reservation/add', function () {
        return redirect()->route('reservation.choose_guest');
    })->name('reservation.addform');
    Route::get('reservation/edit/{id}', 'ReservationController@showEditForm')->name('reservation.editform')->where(['id' => '[0-9]+']);
    Route::post('reservation/edit/{id}', 'ReservationController@postEdit')->name('reservation.postedit')->where(['id' => '[0-9]+']);
    Route::delete('reservation/delete/{id}', 'ReservationController@delete')->name('reservation.delete')->where(['id' => '[0-9]+']);

    Route::get('reservation/choose_guest', 'ReservationController@chooseGuest')->name('reservation.choose_guest');
    Route::get('reservation/search_free_rooms/{guestId}', 'ReservationController@searchFreeRooms')->name('reservation.search_free_rooms')->where(['guestId' => '[0-9]+']);
    Route::post('reservation/search_free_rooms/{guestId}', 'ReservationController@postSearchFreeRooms')->name('reservation.post_search_free_rooms')->where(['guestId' => '[0-9]+']);
    Route::get('reservation/choose_room/{guestId}', 'ReservationController@chooseFreeRoom')->name('reservation.choose_free_room')->where(['guestId' => '[0-9]+']);
    Route::get('reservation/add/{guestId}/{roomId}', 'ReservationController@add')->name('reservation.add')->where(['guestId' => '[0-9]+', 'roomId' => '[0-9]+']);

    Route::get('reservation/edit_choose_guest/{reservationId}', 'ReservationController@editChooseGuest')->name('reservation.edit_choose_guest')->where(['reservationId' => '[0-9]+']);
    Route::get('reservation/edit_change_guest/{reservationId}/{guestId}', 'ReservationController@editChangeGuest')->name('reservation.edit_change_guest')->where(['reservationId' => '[0-9]+', 'guestId' => '[0-9]+']);
    Route::get('reservation/edit_choose_room/{reservationId}', 'ReservationController@editChooseRoom')->name('reservation.edit_choose_room')->where(['reservationId' => '[0-9]+']);
    Route::get('reservation/edit_change_room/{reservationId}/{roomId}', 'ReservationController@editChangeRoom')->name('reservation.edit_change_room')->where(['reservationId' => '[0-9]+', 'roomId' => '[0-9]+']);

    Route::get('user/change_password', 'UserController@changePassword')->name('user.change_password');
    Route::post('user/change_password', 'UserController@postChangePassword')->name('user.post_change_password');
});
