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
Route::get('/device-details/{id}', 'APIController@getMasterDetailsSingleData')->name('api.device_single_details');

Route::get('/master-details', 'APIController@getMasterDetailsData')->name('api.master_details');

Route::get('/master-details/{id}', 'APIController@getMasterDetailsSingleData')->name('api.master_single_details');

Route::get('/column-search', 'APIController@getColumnSearchData')->name('api.column_search');

Route::get('/row-attributes', 'APIController@getRowAttributesData')->name('api.row_attributes');

Route::get('/carbon', 'APIController@getCarbonData')->name('api.carbon');

