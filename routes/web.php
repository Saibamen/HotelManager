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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', 'HomeController@index')->name('home');

    Route::get('room', 'RoomController@index')->name('room.index');
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
    Route::get('reservation/add', 'ReservationController@showAddEditForm')->name('reservation.addform');
    Route::post('reservation/add', 'ReservationController@store')->name('reservation.postadd');
    Route::get('reservation/edit/{id}', 'ReservationController@showAddEditForm')->name('reservation.editform')->where(['id' => '[0-9]+']);
    Route::post('reservation/edit/{id}', 'ReservationController@store')->name('reservation.postedit')->where(['id' => '[0-9]+']);
    Route::delete('reservation/delete/{id}', 'ReservationController@delete')->name('reservation.delete')->where(['id' => '[0-9]+']);

    Route::get('reservation/choose_guest', 'ReservationController@choose_guest')->name('reservation.choose_guest');
    Route::get('reservation/search/{guestId}', 'ReservationController@search')->name('reservation.search')->where(['guestId' => '[0-9]+']);
    Route::get('reservation/choose_room/{guestId}', 'ReservationController@choose_room')->name('reservation.choose_room')->where(['guestId' => '[0-9]+']);
});

Route::get('lang/{language}', 'Controller@changeLanguage')->name('lang.set');
