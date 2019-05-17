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

Route::get('/test/1',function(){
     echo storage_path('app');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/test/get', 'Test\TestController@testGet');

Route::get('/test/upload', 'Test\TestController@upload1');
Route::post('/upload', 'Test\TestController@upload2');


Route::get('/test/sec', 'Test\TestController@secretTest');
Route::get('/test/rsa', 'Test\TestController@rsaTest');
Route::get('/test/sign', 'Test\TestController@testSign');
