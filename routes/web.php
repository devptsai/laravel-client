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

Route::get('/', 'AuthController@index');
Route::get('/login', 'AuthController@login');
Route::post('/login', 'AuthController@cek_auth');
Route::get('/logout', 'AuthController@logout');
Route::get('/siswa/create', 'AuthController@create');
Route::post('/siswa', 'AuthController@store');
Route::delete('/siswa/{id}','AuthController@destroy');
Route::get('/siswa/{id}', 'AuthController@show');
Route::put('/siswa/{id}','AuthController@update');

