<?php

use App\Http\Controllers\Api\v1\BookingController;
use App\Http\Controllers\Api\v1\driver\UsersController;
use App\Http\Controllers\Api\v1\PageController;
use App\Http\Controllers\Api\v1\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});


Route::namespace('Api\v1')->prefix("v1/auth")->name("api.v1.auth")->group(function () {
  Route::post('signup', 'AuthController@signup')->name('signup');
  Route::post('email_login', 'AuthController@email_login')->name('email_login');
  Route::post('resend_code', 'AuthController@resend_code')->name('resend_code');
  Route::post('confirm_code', 'AuthController@confirm_code')->name('confirm_code');
  
  Route::post('mobile_login', 'AuthController@mobile_login')->name('mobile_login');
  Route::post('social_login', 'AuthController@social_login')->name('social_login');

  Route::post('resend_phone_code', 'AuthController@resend_phone_code')->name('resend_phone_code');
  Route::post('confirm_phone_code', 'AuthController@confirm_phone_code')->name('confirm_phone_code');
  //Route::post("/forgot_password", "AuthController@apiForgetPassword");
  //Route::post("/resend_forgot_password_otp", "AuthController@apiResendForgetPasswordOtp");
  //Route::post("/reset_password_otp_verify", "AuthController@apiResetPasswordVerifyOtp");
  //Route::post("/reset_password", "AuthController@apiResetPassword");
  Route::post('delete_user', 'AuthController@delete_account')->name('delete_user');
  Route::post('get_user_by_token', 'AuthController@get_user_by_token')->name('get_user_by_token');
  Route::post('/forgot_password', 'AuthController@forgot_password');
  Route::post('/reset_password_otp_verify', 'AuthController@reset_password_otp_verify')->name('user.reset_password_otp_verify');
  Route::post('/reset_password', 'AuthController@reset_password')->name('user.reset_password');
  Route::post('/resend_forgot_password_otp', 'AuthController@resend_forgot_password_otp')->name('user.resend_forgot_password_otp');
  Route::post('logout', 'AuthController@logout')->name('logout');


  Route::post('get_mobile_otp', 'ChangeMobileController@get_mobile_otp')->name('get_mobile_otp');
  Route::post('resend_mobile_otp',  'ChangeMobileController@resend_mobile_otp')->name('resend_mobile_otp');
  Route::post('change_mobile', 'ChangeMobileController@change_mobile')->name('change_mobile');
});


Route::namespace('Api\v1')->prefix("v1")->name("api.v1.")->group(function () {
  
  Route::post('/my_profile', 'UsersController@my_profile')->name('my_profile');
  Route::post('/update_user_profile', 'UsersController@update_user_profile');
  Route::post('/change_password', 'UsersController@change_password');

  Route::post('home', 'HomeController@index')->name('home');

  Route::post('/add_address', 'UsersController@add_address')->name('add_address');
  Route::post('/edit_address', 'UsersController@edit_address')->name('edit_address');
  Route::post('/delete_address', 'UsersController@delete_address')->name('delete_address');
  Route::post('/list_address', 'UsersController@list_address')->name('list_address');
  Route::post('/set_default', 'UsersController@setdefault')->name('set_default');


  Route::post('/create_order', 'OrdersController@create_order')->name('create_order');
  Route::post('/price_details', 'OrdersController@priceDetails')->name('price_details');
  Route::post('/payment_success', 'OrdersController@paymentSuccess')->name('payment_success');

  Route::post('/my_orders', 'OrdersController@myOrders');
  Route::post('/my_order_details', 'OrdersController@myOrderDetails');
  

  Route::post('/size', 'CMS@sizelist')->name('size');
  Route::post('/care', 'CMS@carelist')->name('care');
  Route::post('/get_page', 'CMS@get_page');
  Route::post('/get_faq', 'CMS@get_faq');
  Route::post('/get_help', 'CMS@gethelp');
  Route::post('/submit_contact_us', 'CMS@submit_contact_us');

//    Route::middleware('')->group(function () {
//
//  });
    Route::prefix('/vehicles')->group(function () {
        Route::post('/list', [VehicleController::class, 'listVehicles']);
        Route::post('/save', [VehicleController::class, 'saveVehicle']);
        Route::post('/delete', [VehicleController::class, 'deleteVehicle']);
        Route::post('/details', [VehicleController::class, 'getVehicle']);
        Route::post('/get-types', [VehicleController::class, 'getTypes']);
        Route::post('/get-manufacturers', [VehicleController::class, 'getManufacturers']);
        Route::post('/get-models', [VehicleController::class, 'getModels']);
        Route::post('/emergency-problems-list', [VehicleController::class, 'getProblems']);
    });

    Route::post('/cms-page', [PageController::class, 'getCMSDetail']);
    Route::post('/social-links', [PageController::class, 'getContactInfo']);
    Route::post('/contact-us', [PageController::class, 'contactUs']);
    Route::post('/place-booking', [BookingController::class, 'placeBooking']);
    Route::post('/get-my-bookings', [BookingController::class, 'getMyBookings']);
    Route::post('/get-booking', [BookingController::class, 'bookingDetails']);
    Route::post('/get-all-orders', [BookingController::class, 'getAllOrders']);
    Route::post('/get-order-details', [BookingController::class, 'getOrderDetails']);
    Route::post('/accept-order', [BookingController::class, 'acceptOrder']);
    Route::post('/payment-init', [BookingController::class, 'createStripePayment']);
    Route::post('/cancel-order', [BookingController::class, 'cancelOrder']);
    Route::post('/add-rating', [BookingController::class, 'addRating']);
    Route::post('/confirm-payment', [BookingController::class, 'confirmPayment']);
    Route::post('/apply-promo-code', [BookingController::class, 'applyPromoCode']);
    Route::post('/cancel-promo-code', [BookingController::class, 'cancelPromoCode']);

    // vendor routes
    Route::post('/place-bid', [BookingController::class, 'placeBid']);
    Route::post('/otp-verify-for-order', [BookingController::class, 'verifyOtp']);
    Route::post('/otp-verify-for-deliver', [BookingController::class, 'verifyOtp']);
    Route::post('/get-all-available-bookings', [BookingController::class, 'getAllAvailableBookings']);
    Route::post('/start-work', [BookingController::class, 'startWork']);
    Route::post('/deliver', [BookingController::class, 'deliver']);
    Route::post('/dashboard', [UsersController::class, 'dashboard']);
    Route::post('/mute-all-bookings', [BookingController::class, 'muteAllBookings']);
    Route::post('/unmute-all-bookings', [BookingController::class, 'unmuteAllBookings']);
    Route::post('/mute-booking', [BookingController::class, 'muteBooking']);
    Route::post('/unmute-booking', [BookingController::class, 'unmuteBooking']);
});


Route::namespace('Api\v1\driver')->prefix("v1/driver/auth")->name("api.v1.driver.auth")->group(function () {
  Route::post('email_login', 'AuthController@email_login')->name('email_login');

  Route::post('/forgot_password', 'AuthController@forgot_password');
  Route::post('/reset_password_otp_verify', 'AuthController@reset_password_otp_verify')->name('user.reset_password_otp_verify');
  Route::post('/reset_password', 'AuthController@reset_password')->name('user.reset_password');
  Route::post('/resend_forgot_password_otp', 'AuthController@resend_forgot_password_otp')->name('user.resend_forgot_password_otp');
  Route::post('logout', 'AuthController@logout')->name('logout');

});

Route::namespace('Api\v1\driver')->prefix("v1/driver")->name("api.v1.driver.")->group(function () {
  Route::post('/my_orders', 'OrdersController@myOrders');
  Route::post('/my_order_details', 'OrdersController@myOrderDetails');
  Route::post('/change_status', 'OrdersController@changeStatus');
  Route::post('/mute', 'OrdersController@muteOrder');

  Route::post('/my_profile', 'UsersController@my_profile')->name('my_profile');
  Route::post('/update_user_profile', 'UsersController@update_user_profile');
  Route::post('/change_password', 'UsersController@change_password');
  
});


Route::namespace('Api\v1')->prefix('v1')->group(function () { 

  require __DIR__ . '/api/v1/Auth/user.php';
  require __DIR__ . '/api/v1/Auth/driver.php';
  require __DIR__ . '/api/v1/Profile/profile.php';
  require __DIR__ . '/api/v1/test.php';
  
});
