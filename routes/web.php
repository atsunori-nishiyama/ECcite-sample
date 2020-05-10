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

// Route::get('/', function () {
//     return view('welcome');
// });

// use App\Http\Controllers\CartItemController;
// use App\Http\Controllers\ItemController;
// use Illuminate\Routing\Route;

// use Illuminate\Routing\Route;

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'ItemController@index');

Route::get('/item/{item}', 'ItemController@show');

Route::post('/cartitem', 'CartItemController@store');

Route::get('/cartitem', 'CartItemController@index');

Route::delete('/cartitem/{cartItem}', 'CartItemController@destroy');

Route::put('/cartitem/{cartItem}', 'CartItemController@update');

Route::get('/buy', 'BuyController@index');

Route::post('/buy', 'BuyController@store');