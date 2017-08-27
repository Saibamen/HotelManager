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
});

Route::get('lang/{language}', 'Controller@changeLanguage')->name('lang.set');
