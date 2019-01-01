<?php

Route::group([
    'prefix' => 'auth'

], function () {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

Route::post('register', 'RegisterController@create');

Route::group([
    'prefix' => 'notes'

], function () {

    Route::get('/', 'NotesController@index');
    Route::post('create', 'NotesController@create');
    Route::put('update/{id}', 'NotesController@update');
    Route::delete('delete/{id}', 'NotesController@delete');

});


