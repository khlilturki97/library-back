<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('books/not-borrowed', 'BookController@notBorrowed');
Route::resource('books', 'BookController')->except(['create', 'edit', 'destroy']);
Route::resource('clients', 'ClientController')->except(['create', 'edit', 'destroy']);
Route::post('borrow', 'BookClientController@store');
Route::put('borrow', 'BookClientController@update');