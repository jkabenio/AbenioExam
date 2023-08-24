<?php

use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => ['web']], function () {
    // Your routes here
    // Route::get('/', 'App\Http\Controllers\ProductController@showProduct');
    Route::get('/show-product', 'App\Http\Controllers\ProductController@showProduct')->name('show-product.show');
    Route::get('/product', 'App\Http\Controllers\ProductController@createProduct')->name('product.create');
    Route::post('/product', 'App\Http\Controllers\ProductController@storeProduct')->name('product.store');
    Route::get('/product/{id}/edit', 'App\Http\Controllers\ProductController@edit')->name('product.edit');
    Route::post('/product/update', 'App\Http\Controllers\ProductController@update')->name('product.update');
    Route::delete('/product/delete/{id}', 'App\Http\Controllers\ProductController@destroy')->name('product.destroy');

});
