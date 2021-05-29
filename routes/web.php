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


Route::get('/', 'DeviceController@getRowDetails')->name('simple')->middleware('auth');
Route::get('/', function () {
    return Redirect::to('speed_report');
})->middleware('auth');

Route::get('/speed_report', 'DeviceController@getSpeedReport')->name('speed_report')->middleware('auth');

Route::get('/power_cut', 'DeviceController@getPowerCut')->name('power_cut')->middleware('auth');

Auth::routes();

Route::get('/logout', function () {
    Auth::logout();
    return Redirect::to('login');
});
