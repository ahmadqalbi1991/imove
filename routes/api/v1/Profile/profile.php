<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->name("api.v1.")->group(function () {
        require __DIR__ . '/user.php';
        require __DIR__ . '/driver.php';
});
