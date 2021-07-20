<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// products
Route::get('/products', 'API\ProductsController@index');


// create orders
Route::get('/order/create', 'API\OrderController@index');

// bot
Route::post('/bot', 'API\BotController@index');
Route::get('/test', 'API\BotController@test');
