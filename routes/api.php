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

Route::post('register', 'LoginController@register');
Route::post('login', 'LoginController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {
	Route::get('active-user', 'LoginController@getAuthenticatedUser');
	Route::Resource('users','UserController');
	Route::Resource('books','BookController');
	Route::get('get-rented-list', 'RentBookController@index');
	Route::post('buy-book', 'RentBookController@store');
	Route::post('return-book', 'RentBookController@return_book');
});

