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



Route::post('register','UserController@register');

Route::middleware('auth:sanctum')->group(function(){

    Route::post('get-product','ProductController@getProduct');


    Route::post('add-cart', 'CartController@addToCart');
    Route::post('get-cart','CartController@getCart');
   
    Route::post('update-cart', 'CartController@updateCart');
    Route::post('delete-cart', 'CartController@deleteCartItem');
    Route::post('clear-cart', 'CartController@clearCart');
    Route::post('checkout', 'CartController@checkout');
    Route::post('order-history', 'CartController@orderHistory');


});
