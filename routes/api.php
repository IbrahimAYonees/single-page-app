<?php


Route::post('register','Auth\AuthController@register');
Route::post('login','Auth\AuthController@login');
Route::post('logout','Auth\AuthController@logout');


Route::group(['middleware' => 'auth'],function(){
    Route::get('me','Auth\AuthController@me');
});
