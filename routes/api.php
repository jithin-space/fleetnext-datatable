<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::get('/speed_report', 'APIController@getSpeedReport')->name('api.speed_report');
Route::get('/power_cut', 'APIController@getPowerCut')->name('api.power_cut');
Route::get('/speed-details/{id}', 'APIController@getSpeedReportSingleData')->name('api.speed_single_details');
Route::get('/power-details/{id}', 'APIController@getPowerReportSingleData')->name('api.power_single_details');
