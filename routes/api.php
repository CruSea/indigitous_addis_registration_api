<?php

use Illuminate\Http\Request;
use LaravelQRCode\Facades\QRCode;
use QR_Code\QR_Code;
use QR_Code\Types\QR_Phone;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



/**
|----------------------------------------------------------------------------------------------------------------------------------------------------
| User Controller API Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
|
|
 */

Route::get('/user', ['uses'=>'AuthController@index']);
Route::post('/user', ['uses'=>'AuthController@create']);
Route::get('/user/login', ['uses'=>'AuthController@login']);
Route::post('/user/authenticate', ['uses'=>'AuthController@authenticate']);
Route::post('/user/update/{id}', ['uses'=>'AuthController@edit']);
Route::delete('/user/{id}', ['uses'=>'AuthController@delete']);


/**
 *
 * Roles
 */
Route::get('/roles', ['uses'=>'RolesController@index']);



/**
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Attendants Controller API Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
|
| Hackathon attendants registration
|
 */

Route::get('/attendant', ['uses'=>'AttendantController@index']);
Route::post('/attendant', ['uses'=>'AttendantController@create']);
Route::get('/attendant/export', ['uses'=>'AttendantController@exportAll']);
Route::get('/attendant/search', ['uses'=>'AttendantController@search']);
Route::get('/attendant/{id}', ['uses'=>'AttendantController@show']);
Route::delete('/attendant/{id}', ['uses'=>'AttendantController@delete']);


