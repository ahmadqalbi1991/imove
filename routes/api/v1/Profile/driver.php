<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->name("api.v1.driver.")->group(function () {
    Route::post('/my_profile', 'UsersController@my_profile')->name('my_profile');
    Route::post('/update_user_profile', 'UsersController@update_user_profile');
    Route::post('/change_password', 'UsersController@change_password');
});
