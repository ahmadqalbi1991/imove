<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\BookingPickUpOrder;

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


Route::get('/testing', function () {
   

    $sqlBuilder = BookingPickUpOrder::select([
        DB::raw('booking_pick_up_orders.id::text as id'),
        DB::raw('booking_pick_up_orders.order_number::text as order_number'),
        DB::raw('booking_pick_up_orders.created_at::text as created_at'),
        DB::raw('categories.name::text as category_name'),
        DB::raw('users.name::text as customer_name'),
        DB::raw('booking_pick_up_orders.booking_status::text as booking_status'),
    ])
        ->leftJoin('categories', 'booking_pick_up_orders.category_id', '=', 'categories.id')
        ->leftJoin('users', 'booking_pick_up_orders.customer_id', '=', 'users.id')
        ->orderBy('booking_pick_up_orders.id', 'DESC');
       

       // The received deliveries
         $received_deliveries = $sqlBuilder->where('booking_status','<=', 8)->get();

         dd($received_deliveries );

    
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('/access-restricted', 'Admin\AuthController@access_restricted')->name('admin.access_restricted');
Route::get('/admin', 'Admin\AuthController@login')->name('admin.login');
Route::post('admin/check_login', 'Admin\AuthController@check_login')->name('admin.check_login');
Route::get('/admin/logout', 'Admin\AuthController@logout')->name('admin.logout');


// socail login page
Route::get('/customer_booking/{id}', 'Admin\BookingController@view_booking_request')->name('user.booking');

//Customer Reset Password
Route::get('/resetPassword/{token}', 'User\AuthController@reset_password')->name('user.reset.password');

Route::post('/setPassword', 'User\AuthController@set_password')->name('user.password_set');

Route::get('/setPassword', 'User\AuthController@set_password')->name('user.password_set');


Route::get('/test', function () {
    return view('test');
});

Route::get('/test2', function () {
    $s=new App\Services\FcmNotificationService();
    return ($s->sendNotification("cdfygh","dsfsd","dasd"));
});

//Google
Route::get('/login/google', [App\Http\Controllers\User\SocailLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [App\Http\Controllers\User\SocailLoginController::class, 'handleGoogleCallback']);
//Facebook
Route::get('/login/facebook', [App\Http\Controllers\User\SocailLoginController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('/login/facebook/callback', [App\Http\Controllers\User\SocailLoginController::class, 'handleFacebookCallback']);
//apple
Route::get('/login/apple', [App\Http\Controllers\User\SocailLoginController::class, 'redirectToApple'])->name('login.apple');
Route::get('/login/apple/callback', [App\Http\Controllers\User\SocailLoginController::class, 'handleAppleCallback']);