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

//email verification
Route::get('/verifyemail/{token}', 'Auth\RegisterController@verify');
Route::post('/setpassword', 'Auth\RegisterController@setPassword')->name('setPassword');
