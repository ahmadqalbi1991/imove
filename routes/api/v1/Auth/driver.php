<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('driver')->name("api.v1.")->middleware('set.role:2')->group(function () {

    Route::post('signup', 'AuthController@signup')->name('signup');
    Route::post('email_login', 'AuthController@email_login')->name('email_login');
    Route::post('resend_code', 'AuthController@resend_code')->name('resend_code');
    Route::post('logout', 'AuthController@logout')->name('logout');
    Route::post('confirm_code', 'AuthController@confirm_code')->name('confirm_code');
    
    Route::post('mobile_login_send_otp', 'AuthController@mobile_login_send_otp')->name('mobile_send_otp_for_login');
    Route::post('mobile_login_with_otp', 'AuthController@mobile_login_with_otp')->name('mobile_login');
    Route::post('social_login', 'AuthController@social_login')->name('social_login');
  
    Route::post('resend_phone_code', 'AuthController@resend_phone_code')->name('resend_phone_code');
    Route::post('verify_signup_otp_by_user_id', 'AuthController@confirm_phone_code')->name('confirm_phone_code');
    Route::post('verify_signup_otp_by_user_phone', 'AuthController@confirm_phone_code')->name('confirm_phone_code');
   
    
    Route::post('verify_otp_after_phone_change', 'AuthController@confirm_temp_phone_code_token_user')->name('confirm_phone_code_token_user');

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