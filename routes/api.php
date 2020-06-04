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

Route::post('/register', 'CredentialApi@register');
Route::post('/login', 'CredentialApi@login');
Route::middleware('auth:api')->get('/logout','CredentialApi@logout');
Route::middleware('auth:api')->get('/userdata','CredentialApi@get_user_data');

Route::middleware('auth:api')->post('/create','StockControllerApi@create');
Route::middleware('auth:api')->post('/view','StockControllerApi@view');
Route::middleware('auth:api')->get('/edit/{id}','StockControllerApi@edit');
Route::middleware('auth:api')->post('/update/{id}','StockControllerApi@update');
Route::middleware('auth:api')->get('/delete/{id}','StockControllerApi@delete');

Route::middleware('auth:api')->post('/history/create','HistoryControllerApi@create');
Route::middleware('auth:api')->post('/history/view','HistoryControllerApi@view');
Route::middleware('auth:api')->get('/history/edit/{id}','HistoryControllerApi@edit');
Route::middleware('auth:api')->post('/history/update/{id}','HistoryControllerApi@update');
Route::middleware('auth:api')->get('/history/delete/{id}','HistoryControllerApi@delete');

