<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['web']], function () {

    Route::get('/', ["as" => "root", function () {
        return view('login');
    }]);

    Route::post('login/', ["as" => "login", function () {
        return redirect()->route('dashboard');
    }]);

    Route::get('dashboard/', ["as" => "dashboard", function () {
        return view('dashboard');
    }]);

});
