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

Route::get('home', 'HomeController@index');

Route::get('room', ['as' => 'room.index', 'uses' => 'RoomController@index']);
Route::get('room/add', ['as' => 'room.addform', 'uses' => 'RoomController@showAddEditForm']);
Route::post('room/add', ['as' => 'room.postadd', 'uses' => 'RoomController@store']);
Route::get('room/edit/{id}', ['as' => 'room.editform', 'uses' => 'RoomController@showAddEditForm'])->where(['id' => '[0-9]+']);
Route::post('room/edit/{id}', ['as' => 'room.postedit', 'uses' => 'RoomController@store'])->where(['id' => '[0-9]+']);
Route::delete('room/delete/{id}', ['as' => 'room.delete', 'uses' => 'RoomController@delete'])->where(['id' => '[0-9]+']);

Route::get('lang/{language}', ['as' => 'lang.set', 'uses' => 'Controller@changeLanguage']);
