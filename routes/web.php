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

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/test/upload', 'Test\TestController@upload1');
Route::get('/test/cdn1', 'Test\TestController@testCdn');
Route::post('/upload', 'Test\TestController@upload2');

Route::get('/test/sec', 'Test\TestController@secretTest');
Route::get('/test/rsa', 'Test\TestController@rsaTest');
Route::get('/test/sign', 'Test\TestController@testSign');

Route::get('/test/100k', 'Test\TestController@insert100k');


Route::get('/order/info', 'Order\OrderController@info');     //订单详情
Route::get('/order/new', 'Order\OrderController@newOrder');     //生成订单

Route::get('/pay/alipay/test', 'Pay\AlipayController@test');       //测试
Route::get('/pay/alipay/pay/{id}', 'Pay\AlipayController@pay');       //去支付
Route::post('/pay/alipay/notify', 'Pay\AlipayController@notify');       //支付宝异步通知
Route::get('/pay/alipay/return', 'Pay\AlipayController@aliReturn');       //支付宝同步通知


Route::get('/alitest', 'Pay\AlipayController@aliTest');







