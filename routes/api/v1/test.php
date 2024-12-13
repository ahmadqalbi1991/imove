<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('test')->name("api.v1.")->group(function () {
       
  Route::get('/testToAddNotificationToUser', [TestingController::class, 'testToAddNotificationToUser']);
  Route::get('/firebase/write', [TestingController::class, 'writeDataToFirbase']);
  Route::get('/firebase/read', [TestingController::class, 'readDataFromFirbase']);
});
