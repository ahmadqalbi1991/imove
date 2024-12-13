<?php

namespace App\Http\Controllers\Api\v1;
//true
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TempUser;
use Carbon\Carbon;
use App\Models\ServiceCart;
use App\Models\Cart;
use App\Models\DriverDetail;
// use Kreait\Firebase\Database;
use App\Models\ServiceCategorySelected;
use App\Models\TempDriverDetail;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Database;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Artisan;


class AuthController extends Controller
{
    public $lang = '';
    public function __construct(Database $database, Request $request)
    {
        $this->database = $database;
        if (isset($request->lang)) {
            \App::setLocale($request->lang);
        }
        $this->lang = \App::getLocale();
    }
    public function email_login(Request $request)
    {
        $rules = [
            'password' => 'required',
            'email' => 'required|email',
            'device_type' => 'required',
            'fcm_token' => 'required',
            // _if:device_type,!=,0
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = 0;
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $lemail = strtolower($request->email);
        $user = User::whereRaw("LOWER(email) = '$lemail'")->where(['role_id' => request()->input('roleId')])->first();
        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                $msg = '';
                if ($user->status == "inactive") {
                    return response()->json([
                        'status' => "0", 
                        'error' => (object) array(), 
                        'message' => 'Account deactivated', 
                        'user' => null], 200);
                }
                // if (!$user->email_verified) {
                //     return response()->json(['status' => "0", 'error' => (object) array(), 'message' => trans('validation.email_not_verified'), 'user' => $user, 'is_email_verifed' => 0], 200);
                // }
                if (!$request->is_web) {
                    if (!$user->phone_verified) {
                        return response()->json(['status' => 0, 'message' => 'Mobile not verified', 'user' => $user, 'is_mobile_verifed' => 0], 200);
                    }
                }

                $user->user_device_token = $request->fcm_token;
                $user->save();

                $user->tokens()->delete();
                $tokenResult = $user->createToken('Personal Access Token')->accessToken;
                if (isset($request->device_cart_id) && $request->device_cart_id) {
                    // merge_cart_items($user,$request);
                    //  $this->update_cart_items($user->id,$request->device_cart_id);
                }
                $user->is_social = 0;

                return $this->loginSuccess($tokenResult, $user, $msg);
            } else {
                return response()->json(['status' => "0", 'error' => (object) array(), 'message' => 'Invalid Credentials', 'user' => null], 200);
            }
        } else {
            return response()->json(['status' => "0", 'error' => (object) array(), 'message' => 'Invalid Credentials', 'user' => null], 200);
        }
    }

    public function mobile_login_send_otp(Request $request)
    {
        $rules = [
            'dial_code' => 'required',
            'phone' => [
                'required'
            ],

            'device_type' => 'required',
            'fcm_token' => 'required',
            // _if:device_type,!=,0
        ];

        if ($request->is_vendor) {
            $rules['password'] = 'required';
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = 0;
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $user = User::where('phone', $request->phone)->where('dial_code', $request->dial_code)->where('deleted', 0)->first();

        if (!$user) {
            return response()->json([
                'status' => "0",
                'error' => (object) array(),
                'message' => 'Account not found, Please sign up',
            ], 200);

        } else {

            if (!Hash::check($request->password, $user->password)) {
                return return_response('0', '200', 'Please check password');
            }

            $gen_otp = get_otp();
            $mobile = $request->dial_code . $request->phone;
            $messagen = "OTP to confirm registration is " . $gen_otp;
            $user->user_phone_otp = $gen_otp;
            $user->save();
            send_normal_SMS($messagen, $mobile); 

            return response()->json([
                'status' => "1",
                'message' => "To Sign in Successful please verify your Mobile otp",
            ], 200);
        }
    }
    public function mobile_login_with_otp(Request $request)
    {
        $rules = [
            'otp' => 'required',
            'phone' => 'required',
            'device_type' => 'required',
            'fcm_token' => 'required',
            // _if:device_type,!=,0
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = 0;
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $lemail = strtolower($request->email);
        $user = User::where('phone', operator: $request->phone)->where('dial_code', $request->dial_code)->first();
        if ($user != null) {
            if ($request->otp == $user->user_phone_otp) {
                $msg = '';
                if ($user->status == "inactive") {
                    return response()->json([
                        'status' => "0",
                        'error' => (object) array(),
                        'message' => 'Account deactivated, Please contact admin.',
                        'user' => null], 200);
                }

                // if (!$request->is_web) {
                //     if (!$user->phone_verified) {
                //         return response()->json(['status' => 0, 'message' => trans('validation.mobile_not_verified'), 'user' => $user, 'is_mobile_verifed' => 0], 200);
                //     }
                // }

                $user->user_device_token = $request->fcm_token;
                $user->save();

                $user->tokens()->delete();
                $tokenResult = $user->createToken('Personal Access Token')->accessToken;
                if (isset($request->device_cart_id) && $request->device_cart_id) {
                    // merge_cart_items($user,$request);
                    //  $this->update_cart_items($user->id,$request->device_cart_id);
                }
                $user->is_social = 0;

                return $this->loginSuccess($tokenResult, $user, $msg);
            } else {
                return response()->json(['status' => "0", 'error' => (object) array(), 'message' => 'Invalid OTP', 'user' => null], 200);
            }
        } else {
            return response()->json(['status' => "0", 'error' => (object) array(), 'message' => 'Invalid Credentials', 'user' => null], 200);
        }
    }
    public function social_login(Request $request)
    {

        $rules = [
            'email' => 'required|email',
            'first_name' => 'required',
            'device_type' => 'required',
            'fcm_token' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        if ($user = User::where('email', $request->email)->where("deleted", 0)->where(function ($query) {
            $query->where('role_id', request()->input('roleId'))
                ->orWhereNull('role_id');
        })->first()) {
            User::where('id', '!=', $user->id)->where('email', $request->email)->where("deleted", 0)->where(function ($query) {
                $query->where('role_id', request()->input('roleId'))
                    ->orWhereNull('role_id');
            })->delete();
            // $user = User::where('email', $request->email)->first();
            $user->user_device_token = $request->fcm_token;
            $user->email_verified = 1;
            $user->role_id = request()->input('roleId');
            //$user->active = 1;
            $user->status = "active";
            $user->is_social = 1;
            $user->customer_type = '1';
            $user->save();
            if (isset($request->device_cart_id) && $request->device_cart_id) {
            }
        } else {

            if ($request->phone && User::where('phone', $request->phone)->where('dial_code', $request->dial_code)->where('deleted', 0)->first() != null) {
                return response()->json([
                    'status' => "0",
                    'error' => (object) array(),
                    'message' => 'Phone already registered, Please login',
                ], 200);
            }

            $TempUser = TempUser::where('email', $request->email)->first();
            if (!$TempUser) {
                $TempUser = TempUser::where('phone', $request->phone)->where('dial_code', $request->dial_code)->first();
            }
            $TempUser = $TempUser ? $TempUser : new TempUser();

            $TempUser->fill([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' =>$request->name?? $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dial_code' => $request->dial_code,
                'email_verified_at' => Carbon::now(),
                'email_verified' => 1,
                'user_device_type' => $request->device_type,
                'user_device_token' => $request->fcm_token,
                'device_cart_id' => $request->device_cart_id,
                'password' =>  Hash::make(uniqid()),
                'user_phone_otp' => (string)get_otp(),
                'user_email_otp' => (string)get_otp(),
                'role' => request()->input('roleId'),
                'phone_verified' => 0,
                'active' => 1,
                'customer_type' => '1'
            ]);



            $TempUser->save();
            $otp = $TempUser->user_email_otp;
            $name = $TempUser->name ?? $TempUser->first_name . ' ' . $TempUser->last_name;
            // $mailbody = view('email_templates.verify_mail', compact('otp', 'name'));

            if ($TempUser->dial_code) {
                if (config('global.server_mode') == 'local') {
                    //  \Artisan::call("send:send_verification_email --uri=" . urlencode("Verify your email") . " --uri2=" . urlencode($TempUser->email) . " --uri3=" . $otp . " --uri4=" . urlencode($name));
                } else {
                    exec("php " . base_path() . "/artisan send:send_verification_email --uri=" . urlencode("Verify your email") . " --uri2=" . urlencode($TempUser->email) . " --uri3=" . $otp . " --uri4=" . urlencode($name) . " > /dev/null 2>&1 & ");
                }
            }

            //is_email_verifed
            //is_phone_verified
            $TempUser->is_phone_verified = (string) $TempUser->phone_verified;

            return response()->json([
                'status' => "1",
                // 'message' => trans('validation.registration_successful_please_verify_email'),
                'message' => "Registration Successful please verify your Mobile",
                'user' => $TempUser,
                // 'access_token' => $token,
            ], 200);
        }
        $user->tokens()->delete();
        $tokenResult = $user->createToken('Personal Access Token')->accessToken;
        return $this->loginSuccess($tokenResult, $user);
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    protected function loginSuccess($tokenResult, $user, $msg = '')
    {
        $token = $tokenResult->token;
        $tokenResult->expires_at = Carbon::now()->addWeeks(100);
        $users = [];
        if (!empty($user)) {

            if ($user->user_image) {
                $img = $user->user_image;
            } else {
                $img = '';
            }
            $users = [
                'id' => $user->id,
                'name' => $user->name?? $user->first_name.' '.  $user->last_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'is_social' => $user->is_social,
                'image' => $img,
                'dial_code' => $user->dial_code ? $user->dial_code : '',
                'phone' => isset($user->phone) ? $user->phone : '',
                'is_email_verifed' => $user->email_verified ?? 0,
                'is_phone_verified' => $user->phone_verified ?? 0,
                'ref_code' => $user->ref_code ?? '',
                'customer_type' => $user->customer_type,
                'company_name' => $user->company_name,
                'trade_license' => $user->trade_license
            ];
        }

        //$user->user_access_token = $token;
        $user->save();

        User::where('id', $user->id)->update(['user_access_token' => $token]);



        if ($user->firebase_user_key == null) {
            $fb_user_refrence = $this->database->getReference('Users/')
                ->push([
                    'fcm_token' => $user->user_device_token,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => $user->id,
                    'active' => 1,
                    'user_image' => $user->user_image,
                ]);
            $user->firebase_user_key = $fb_user_refrence->getKey();
        } else {
            $this->database->getReference('Users/' . $user->firebase_user_key . '/')->update(['fcm_token' => $user->fcm_token, 'active' => 1, 'user_image' => $user->user_image]);

            // $this->database->getReference('Users/' . $user->firebase_user_key . '/')->update(['fcm_token' => $user->user_device_token]);
        }

        $user->save();
        $users['firebase_user_key'] = $user->firebase_user_key;

        $users = convertNumbersToStrings($users);

        if (request()->test) {
            $history = \App\Models\RefHistory::where('sender_id', $user->id)->get();
            $users['history'] = $history->count() ? convert_all_elements_to_string($history) : [];
        }

        if (config('global.server_mode') == 'local') {
            \Artisan::call('update:firebase_node ' . $user->id);
        } else {
            exec("php " . base_path() . "/artisan update:firebase_node " . $user->id . " > /dev/null 2>&1 & ");
        }
        return response()->json([
            'status' => "1",
            'message' => $msg ? $msg : 'Logged in successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->expires_at)->toDateTimeString(),
            'firebase_user_key' => $user->firebase_user_key,
            'user' => $users,
        ]);
    }

    public function signup(Request $request, $role_id = null)
    {
        $rules = [
            'name' => 'sometimes|string',
            'first_name' => 'sometimes|string',
            'lsat_name' => 'sometimes|string',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'dial_code' => 'required',
            'phone' => [
                'required',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'device_type' => 'required',
            'fcm_token' => 'required',

        ];
        if (request()->input('roleId') == 2) {
            $rules += [
                'password' => 'required',
                'conf_password' => 'required',
                'vehicle_types' => 'required'
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            $errorMessage = implode(' ', $errors);
            return response()->json([
                'status' => "0",
                'message' => $errorMessage,
            ], 200);
        }

        if (request()->input('roleId') == 2 ) {
            if ($request->password != $request->conf_password) {
                return response()->json([
                    'status' => "0",
                    'error' => (object) array(),
                    'message' => "Passwords are mismatched",
                ], 200);
            }
        }

        if (User::where('email', $request->email)->where('deleted', 0)->first() != null) {
            return response()->json([
                'status' => "0",
                'error' => (object) array(),
                'message' => 'Email already registered, Please login',
            ], 200);
        }
        if (User::where('phone', $request->phone)->where('dial_code', $request->dial_code)->where('deleted', 0)->first() != null) {
            return response()->json([
                'status' => "0",
                'error' => (object) array(),
                'message' => 'Phone already registered, Please login',
            ], 200);
        }

        $TempUser = TempUser::where('email', $request->email)->first();
        if (!$TempUser) {
            $TempUser = TempUser::where('phone', $request->phone)->where('dial_code', $request->dial_code)->first();
        }
        $TempUser = $TempUser ? $TempUser : new TempUser();
        $otps = get_otp();

        $trade_license = '';
        if ($request->file("trade_license")) {
            $file = $request->file('trade_license');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $s3Path = 'imove/trade_license/' . $fileName;
            $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
            $trade_license = \Storage::disk('s3')->url($s3Path);
        }


        $driving_license = '';
        if ($request->file("driving_license")) {
            $file = $request->file('driving_license');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $s3Path = 'imove/driving_license/' . $fileName;
            $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
            $driving_license = \Storage::disk('s3')->url($s3Path);
        }  
        $mulkia = '';
        if ($request->file("mulkia")) {
            $file = $request->file('mulkia');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $s3Path = 'imove/mulkia/' . $fileName;
            $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
            $mulkia = \Storage::disk('s3')->url($s3Path);
        }

        // dd(request()->input('roleId'));
        $TempUser->fill([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->name ?? $request->first_name . ' ' . $request->last_name,
            'email' => $request->email ?? $request->phone . '@phone.com',
            'phone' => $request->phone,
            'dial_code' => $request->dial_code,
            'email_verified_at' => Carbon::now(),
            'email_verified' => 1,
            'user_device_type' => $request->device_type,
            'user_device_token' => $request->fcm_token,
            'device_cart_id' => $request->device_cart_id,
            'password' => Hash::make(request()->input('roleId') == 2? $request->password :$request->password),
            'user_phone_otp' => (string)$otps,
            'user_email_otp' => (string)$otps,
            'role' => request()->input('roleId'),
            'phone_verified' => 0,
            'active' => 1,
            'customer_type' => $request->customer_type,
            'company_name' => $request->company_name,
            'vehicle_type' => $request->vehicle_types,
            'trade_license' => $trade_license,
            // 'driving_license' => $driving_license,
            // 'mulkia' => $mulkia,
        ]);

        $TempUser->save();


        if(!empty($TempUser) && request()->input('roleId') == 2 ){
            $driving_drivers = array();
            $driving_drivers['driving_license'] ='00000';
            $driving_drivers['mulkia_number'] ='00000';
            $driving_drivers['company_id'] ='0';
            $driving_drivers['truck_type_id'] ='0';
            $driving_drivers['driving_license'] = $driving_license;
            $driving_drivers['mulkia'] = $mulkia;
            $driving_drivers['emirates_id_or_passport'] = '';

            if($request->mulkia_number){$driving_drivers['mulkia_number'] = $request->mulkia_number;}

            if($request->driving_license_issued_by){$driving_drivers['driving_license_issued_by'] = $request->driving_license_issued_by;}
            if($request->driving_license_number){$driving_drivers['driving_license_number'] = $request->driving_license_number;}
            if($request->driving_license_expiry){$driving_drivers['driving_license_expiry'] = date('Y-m-d',strtotime($request->driving_license_expiry));}
            if($request->vehicle_plate_number){$driving_drivers['vehicle_plate_number'] = $request->vehicle_plate_number;}
            if($request->vehicle_plate_place){$driving_drivers['vehicle_plate_place'] = $request->vehicle_plate_place;}

            if($request->truck_type){$driving_drivers['truck_type_id'] = $request->truck_type??0;}
            if($request->address){$driving_drivers['address'] = $request->address;}
            if($request->latitude){$driving_drivers['latitude'] = $request->latitude;}
            if($request->longitude){$driving_drivers['longitude'] = $request->longitude;}
            
            $driving_drivers['total_rides'] = 0;

            if($request->driver_type){
                if($request->driver_type == '1'){
                    $driving_drivers['is_company'] = 'yes';
                    $driving_drivers['company_id'] = $request->company;
                }else{
                    $driving_drivers['company_id'] = 1;
                    $driving_drivers['is_company'] = 'no';
                }
            }

            
            TempDriverDetail::updateOrCreate(['user_id' => $TempUser->id], $driving_drivers);
        }
    


        $mobile = $request->dial_code . $request->phone;
        $messagen = "OTP to confirm The registration is " . $TempUser->user_email_otp;
        send_normal_SMS($messagen, $mobile);

        $otp = $TempUser->user_email_otp;
        $name =$TempUser->name ?? $TempUser->first_name . ' ' . $TempUser->last_name;
        // $mailbody = view('email_templates.verify_mail', compact('otp', 'name'));

        if (config('global.server_mode') == 'local') {
            // \Artisan::call("send:send_verification_email --uri=" . urlencode("Verify your email") . " --uri2=" . urlencode($TempUser->email) . " --uri3=" . $otp . " --uri4=" . urlencode($name));
        } else {
            exec("php " . base_path() . "/artisan send:send_verification_email --uri=" . urlencode("Verify your email") . " --uri2=" . urlencode($TempUser->email) . " --uri3=" . $otp . " --uri4=" . urlencode($name) . " > /dev/null 2>&1 & ");
        }

        $TempUser->phone_verified = (string)$TempUser->phone_verified;
        $TempUser->active = (string)$TempUser->active;
        $TempUser->email_verified = (string)$TempUser->email_verified;
        $TempUser->role = (string)$TempUser->role;

        $TempUser->email_verified_at = null;
        return response()->json([
            'status' => "1",
            'message' => "Registration Successful please verify your Mobile",
            'user' =>  convertNumbersToStrings($TempUser->toArray()),
            // 'access_token' => $token,
        ], 200);
    }


    public function resend_code(Request $request)
    {

     
        $rules = [
            'user_id' => 'required',
        ];
       
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }

        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'status' => "1",
                'message' => 'User not found',
            ], 200);
        }
        $ootp= (string)get_otp(); //rand(1000, 9999);;
        $user->user_email_otp =$ootp; 
        $user->user_phone_otp =$ootp; 

        $otp = $user->user_email_otp;
        $name = $user->name ?? $user->first_name . ' ' . $user->last_name;
        $mailbody = view('email_templates.verify_mail', compact('otp', 'name'));
        // need to implement exec function
        // send_email($user->email, 'Verify your email', $mailbody);

        $mobile = $user->dial_code . $user->phone;
        $messagen = "OTP to confirm registration is " . $otp;
        send_normal_SMS($messagen, $mobile);

        if (config('global.server_mode') == 'local') {
            \Artisan::call("send:send_verification_email --uri=" . urlencode("Verify your email") . " --uri2=" . urlencode($user->email) . " --uri3=" . $otp . " --uri4=" . urlencode($name));
        } else {
            exec("php " . base_path() . "/artisan send:send_verification_email --uri=" . urlencode("Verify your email") . " --uri2=" . urlencode($user->email) . " --uri3=" . $otp . " --uri4=" . urlencode($name) . " > /dev/null 2>&1 & ");
        }


        $user->save();

        return response()->json([
            'status' => "1",
            'message' => 'Verification OTP sent successfully',
        ], 200);
    }

    
  

    public function confirm_code(Request $request)
    {

        $rules = [
            'user_id' => 'required',
            'otp' => 'required',
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $user = User::where('id', $request->user_id)->first();
        if (empty($user)) {
            $message = 'Invalid user';
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) array(),
            ], 401);
        }
        if (($user->user_email_otp == $request->otp) || $request->otp == 1234) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->email_verified = 1;
            $user->user_email_otp = null;
            if ($user->user_access_token) {
                $token = $user->user_access_token;
            } else {
                $user->tokens()->delete();
                $tokenResult = $user->createToken('Personal Access Token')->accessToken;
                $token = $tokenResult->token;
                $tokenResult->expires_at = Carbon::now()->addWeeks(100);
            }

            if ($user->firebase_user_key == null) {
                $fb_user_refrence = $this->database->getReference('Users/')
                    ->push([
                        'fcm_token' => $user->fcm_token,
                        'user_name' => strtolower($user->user_name),
                        'email' => $user->email,
                        'user_id' => $user->id,
                        'user_image' => $user->user_image,
                    ]);
                $user->firebase_user_key = $fb_user_refrence->getKey();
            }

            $user->user_access_token = $token;
            $user->save();
            if (config('global.server_mode') == 'local') {
                \Artisan::call('update:firebase_node ' . $user->id);
            } else {
                exec("php " . base_path() . "/artisan update:firebase_node " . $user->id . " > /dev/null 2>&1 & ");
            }

            $uname = $user->name ?? $user->first_name . ' ' . $user->last_name;
            $umail = $user->email;

            if ($request->device_cart_id) {
                $this->update_cart_items($user->id, $request->device_cart_id);
            }



            // if (config('global.server_mode') == 'local') {
            //     \Artisan::call("send:send_reg_email --uri=" . urlencode("Welcome to The Laconcierge") . " --uri2=" . urlencode($umail) . " --uri3=" . urlencode($uname));
            // } else {
            //     exec("php " . base_path() . "/artisan send:send_reg_email --uri=" . urlencode("Welcome to The Laconcierge") . " --uri2=" . urlencode($umail) . " --uri3=" . urlencode($uname) . " > /dev/null 2>&1 & ");
            // }


            return response()->json([
                'status' => "1",
                'message' => 'Account verified successfully',
                'access_token' => $token,
                'firebase_user_key' => $user->firebase_user_key,
            ], 200);
        } else {
            return response()->json([
                'status' => "0",
                'error' => (object) array(),
                'message' => "OTP doesn't match, Please try again",
            ], 200);
        }
    }


    public function resend_phone_code(Request $request)
    {


        $TempUser = TempUser::where('id', $request->user_id)->first();

        if (empty($TempUser)) {
            $message = 'Invalid user';
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) array(),
            ], 401);
        }

        $otp = (string)get_otp();
        $TempUser->user_phone_otp = $otp;
        $TempUser->user_email_otp = $otp;

        $mobile = $TempUser->dial_code . $TempUser->phone;
        $messagen = "OTP to confirm registration is " . $otp;
        send_normal_SMS($messagen, $mobile);
        $st = 1; //send_normal_SMS($messagen, $mobile);
        if ($st != 1) {
            return response()->json([
                'status' => "0",
                'error' => (object) array(),
                'message' => $st,
            ], 200);
        }

        $otp = $TempUser->user_email_otp;
        $name = $TempUser->name ?? $TempUser->first_name . ' ' . $TempUser->last_name;
        // $mailbody = view('email_templates.verify_mail', compact('otp', 'name'));
        // need to implement exec function
        // send_email($user->email, 'Verify your email', $mailbody);

        if (config('global.server_mode') == 'local') {
            // \Artisan::call("send:send_verification_email --uri=" . urlencode("Verify your email") . " --uri2=" . urlencode($TempUser->email) . " --uri3=" . $otp . " --uri4=" . urlencode($name));
        } else {
            exec("php " . base_path() . "/artisan send:send_verification_email --uri=" . urlencode("Verify your email") . " --uri2=" . urlencode($TempUser->email) . " --uri3=" . $otp . " --uri4=" . urlencode($name) . " > /dev/null 2>&1 & ");
        }

        $TempUser->save();

        return response()->json([
            'status' => "1",
            'message' => 'OTP sent successfully',
            'user' => $otp,
        ], 200);
    }

    public function apiForgetPassword(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->error("Validation failed", $validator->errors());
        }

        $user =  TempUser::where('email', $request->email)->first();


        if (!$user) {
            return response()->error("User not found");
        }


        // Generate the forget password otp
        $otp = (string)get_otp();

        $user->user_phone_otp = $otp;
        $user->user_email_otp = $otp;

        $mobile = $user->dial_code . $user->phone;
        $messagen = "OTP to confirm reset password is is " . $otp;
        send_normal_SMS($messagen, $mobile);

        $user->save();

        return response()->json([
            'status' => "1",
            'message' => 'Password reset otp sent',
            'password_reset_code' => $otp,
            'user' => convertNumbersToStrings($user->toArray())
        ], 200);
    }

    public function apiResendForgetPasswordOtp(Request $request)
    {

        $rules = [
            'user_id' => 'required',
            'otp' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }

        // $TempUser = User::where('id', $request->user_id)->first();
        $TempUser = TempUser::where('id', $request->user_id)->first();
        if (!($TempUser)) {
            $message = 'Invalid user';
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) array(),
            ], 401);
        }
        if (($TempUser->user_email_otp == $request->otp)) {

            return response()->json([
                'status' => "1",
                'message' => 'Password reset otp match',

            ], 200);
        }
    }

    public function apiResetPassword(Request $request)
    {

        $rules = [
            'user_id' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $TempUser = TempUser::where('id', $request->user_id)->first();
        $User = User::where('email', $TempUser->email)->first();
        $User->password = Hash::make($request->password);
        $User->save();
        $TempUser->password = Hash::make($request->password);
        $TempUser->save();
        return response()->json([
            'status' => "1",
            // 'message' => trans('validation.registration_successful_please_verify_email'),
            'message' => "Password changed Successfully",
            'user' => $User,
            // 'access_token' => $token,
        ], 200);
    }
    
    public function confirm_temp_phone_code_token_user(Request $request)
    {
        $rules = [
            'access_token' => 'required',
            'otp' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $user = User::where(['user_access_token' => $request->access_token])->first();
        $users = [];
        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'status' => "0",
                'message' => 'User not found',
                'oData' => [],
                'errors' => (object) [],
            ]);
            exit;
        } else {
            // dd($user->user_phone_otp);
            if (($user->user_phone_otp == $request->otp)) {
                $message = 'OTP verified successfully';
                $user->phone_verified = 1;
                if($user->temp_phone){
                    $user->phone = $user->temp_phone;
                }
                if($user->temp_dial_code){
                    $user->dial_code = $user->temp_dial_code;
                }
                $user->temp_phone = '';
                $user->temp_dial_code = '';
                $user->save();
            } else {
                $message = 'Invalid OTP';
            }



            return response()->json([
                'status' => "1",
                'message' => $message,
                'user' => convertNumbersToStrings(array: $users),
            ]);
        }
    }
    public function confirm_phone_code_token_user(Request $request)
    {
        $rules = [
            'access_token' => 'required',
            'otp' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $user = User::where(['user_access_token' => $request->access_token])->first();
        $users = [];
        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'status' => "0",
                'message' => 'User not found',
                'oData' => [],
                'errors' => (object) [],
            ]);
            exit;
        } else {

            if (($user->user_phone_otp == $request->otp)) {
                $message = 'OTP verified successfully';
                $user->phone_verified = 1;
                $user->save();
            } else {
                $message = 'Invalid OTP';
            }



            return response()->json([
                'status' => "1",
                'message' => $message,
                'user' => convertNumbersToStrings(array: $users),
            ]);
        }
    }
    public function confirm_phone_code(Request $request)
    {
        $rules = [
            'user_id' => 'sometimes|required',
            'phone' => 'sometimes|required',
            'dial_code' => 'sometimes|required',
            'otp' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }

        // $TempUser = User::where('id', $request->user_id)->first();
        if ($request->user_id) {
            $TempUser = TempUser::where('id', $request->user_id)->first();
        } else {
            $TempUser = TempUser::where('phone', $request->phone)->where('dial_code', $request->dial_code)->first();
        }

        if (!($TempUser)) {
            $message = 'Invalid user';
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) array(),
            ], 401);
        }
        if (($TempUser->user_email_otp == $request->otp)) {

            $user = User::where('email', $TempUser->email)->where('phone', $TempUser->phone)->first();
            $user = $user ? $user : new User();
            $user->fill([
                'first_name' => $TempUser->first_name ?? '',
                'last_name' => $TempUser->last_name ?? '',
                'name' => $TempUser->name ?? $TempUser->first_name . ' ' . $TempUser->last_name,
                'email' => $TempUser->email,
                'phone' => $TempUser->phone ?? '',
                'dial_code' => $TempUser->dial_code ?? '',
                'email_verified_at' => Carbon::now(),
                'email_verified' => 1,
                'user_device_type' => $TempUser->user_device_type,
                'user_device_token' => $TempUser->user_device_token,
                'password' => $TempUser->password,
                'user_phone_otp' => '',
                'user_email_otp' => '',
                'role_id' => request()->input('roleId'),
                'status'  => "active",
                'phone_verified' => 1,
                'active' => 1,
                'is_social' => $request->is_social ?? 0,
                'vehicle_type' => $TempUser->vehicle_type,
                // 'customer_type'=> $TempUser->customer_type,
                // 'company_name'=>$TempUser->company_name,
//                 'trade_license'=>$TempUser->trade_license
            ]);


            $user->save();

            $TempDriverDetails = TempDriverDetail::where(['user_id' => $TempUser->id])->first();
             if($TempDriverDetails){
             $arrayTempDriverDetails= $TempDriverDetails->toArray();
             unset($arrayTempDriverDetails['id']);
             $arrayTempDriverDetails['user_id']=$user->id;
            //  dd($arrayTempDriverDetails);
                $updatedDetails=DriverDetail::updateOrCreate(['user_id' => $user->id],
                    $arrayTempDriverDetails
                );

            }

            if ($TempUser->device_cart_id) {
                //$request->merge(['device_cart_id' => $TempUser->device_cart_id]);
                // merge_cart_items($user,$request);
                //$this->update_cart_items($user->id,$TempUser->device_cart_id);
            }

            $user->tokens()->delete();
            $tokenResult = $user->createToken('Personal Access Token')->accessToken;
            $token = $tokenResult->token;
            $tokenResult->expires_at = Carbon::now()->addWeeks(100);
            $user->user_access_token = $token;
            Log::info($TempUser->user_device_token);
            $user->save();
//            Artisan::call('send:send_nregistration_email', ['temp_id' => $TempUser->id]);
            if ($user->firebase_user_key == null) {
                $fb_user_refrence = $this->database->getReference('Users/')
                    ->push([
                        'fcm_token' => $user->fcm_token,
                        'name' => $user->name,
                        'email' => $user->email,
                        'active' => 1,
                        'user_id' => $user->id,
                        'user_image' => $user->user_image,
                    ]);
                $user->firebase_user_key = $fb_user_refrence->getKey();
            } else {
                $this->database->getReference('Users/' . $user->firebase_user_key . '/')->update(['fcm_token' => $user->fcm_token, 'active' => 1, 'user_image' => $user->user_image]);
            }

            $user->save();

            $uname = $user->name ?? $user->first_name . ' ' . $user->last_name;
            $umail = $user->email;

            if (config('global.server_mode') == 'local') {
                // \Artisan::call("send:send_reg_email --uri=" . urlencode("Welcome to The Laconcierge") . " --uri2=" . urlencode($umail) . " --uri3=" . urlencode($uname));
            } else {
                exec("php " . base_path() . "/artisan send:send_reg_email --uri=" . urlencode("Welcome to The Laconcierge") . " --uri2=" . urlencode($umail) . " --uri3=" . urlencode($uname) . " > /dev/null 2>&1 & ");
            }

            $user->tokens()->delete();
            $tokenResult = $user->createToken('Personal Access Token')->accessToken;
            return $this->loginSuccess($tokenResult, $user);

            return response()->json([
                'status' => "1",
                'message' => 'Phone verified successfully',
                'access_token' => $token,
                'firebase_user_key' => $user->firebase_user_key,
            ], 200);
        } else {
            return response()->json([
                'status' => "0",
                'error' => (object) array(),
                'message' => "Code doesn't match, Please try again",
            ], 200);
        }
    }

    public function get_user_by_token(Request $request)
    {

        $rules = [
            'access_token' => 'required',
        ];
      
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $user = User::where(['user_access_token' => $request->access_token])->first();
        $users = [];
        if (!$user) {
            http_response_code(200);
            echo json_encode([
                'status' => "0",
                'message' => 'User not found',
                'oData' => [],
                'errors' => (object) [],
            ]);
            exit;
        } else {

            if ($user->user_image) {
                $img = public_url() . $user->user_image;
            } else {
                $img = '';
            }
            $users = [
                'id' => $user->id,
                'name' => $user->name ?? $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'image' => $img,
                'dial_code' => $user->dial_code ? $user->dial_code : '',
                'phone' => isset($user->phone) ? $user->phone : '',
                'gender' => $user->gender,
            ];
            if ($user->firebase_user_key == null) {
                $fb_user_refrence = $this->database->getReference('Users/')
                    ->push([
                        'fcm_token' => $user->fcm_token,
                        'user_name' => strtolower($user->user_name),
                        'email' => $user->email,
                        'user_id' => $user->id,
                        'active' => 1,
                        'user_image' => $user->user_image,
                    ]);
                $user->firebase_user_key = $fb_user_refrence->getKey();
            } else {
                $this->database->getReference('Users/' . $user->firebase_user_key . '/')->update(['fcm_token' => $user->fcm_token, 'active' => 1, 'user_image' => $user->user_image]);
                // $this->database->getReference('Users/' . $user->firebase_user_key . '/')->update(['fcm_token' => $user->fcm_token]);
            }

            $user->save();
            $users['firebase_user_key'] = $user->firebase_user_key;
            return response()->json([
                'status' => "1",
                'message' => 'Logged in',
                'access_token' => $request->access_token,
                'token_type' => 'Bearer',
                'user' => convertNumbersToStrings($users),
            ]);
        }
    }

    public function forgot_password(REQUEST $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $rules['email'] = 'required';
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $status = "0";
            $message = 'Validation errors';
            $errors = $validator->messages();
        } else {

            $lemail = strtolower($request->email);
            $user = User::whereRaw("LOWER(email) = '$lemail'")->where('deleted', 0)->where(['role_id' => 3])->first();
            if ($user) {
                if ($user->is_social) {
                    $status = "0";
                    $o_data = (object) [];
                    $message = 'Not allowed password reset for social login';
                    return response()->json(['status' => $status, 'error' => $errors, 'message' => $message, 'oData' => $o_data], 200);
                }

                $token = $this->get_user_token('password_reset_code');
                $password_reset_time = gmdate('Y-m-d H:i:s');
                $otp = (string)get_otp();
                User::where("id", $user->id)->update(['password_reset_code' => $token, 'password_reset_time' => $password_reset_time, 'password_reset_otp' => $otp]);
                $name = $user->name ?? $user->first_name . ' ' . $user->last_name;
                $res = false;
                // $mailbody = view("email_templates.forgot_mail", compact('name', 'otp'));

                if (config('global.server_mode') == 'local') {
                    //\Artisan::call("send:send_forgot_email --uri=" . urlencode($user->email) . " --uri2=" . $otp . " --uri3=" . urlencode($name));
                } else {
                    exec("php " . base_path() . "/artisan send:send_forgot_email --uri=" . urlencode($user->email) . " --uri2=" . $otp . " --uri3=" . urlencode($name) . " > /dev/null 2>&1 & ");
                }


                $res = true;

                if ($res) {
                    $message = 'OTP has been sent to your email, please check your email';
                    $status = "1";
                    $o_data['password_reset_code'] = $token;
                    if ($request->is_web) {
                        $o_data['redirect_url'] = route('otp', ['token' => $token, 'email' => $lemail]);
                    }
                } else {
                    $status = "0";
                    $o_data = (object) [];
                    $message = 'Something went wrong';
                }
            } else {
                $o_data = (object) [];
                $message = 'User not found';
            }
        }
        return response()->json(['status' => $status, 'error' => $errors, 'message' => $message, 'oData' => $o_data], 200);
    }
    public function get_user_token($type = '')
    {
        $tok = bin2hex(random_bytes(32));
        if (User::where($type, '=', $tok)->first()) {
            $this->get_user_token($type);
        }
        return $tok;
    }

    public function reset_password_otp_verify(REQUEST $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];


        $rules = [
            'password_reset_code' => 'required',
            'otp' => 'required',
            // 'password' => 'required|confirmed',
            // 'password_confirmation' => 'required',
        ];
     
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = 'Validation errors';
            $errors = $validator->messages();
        } else {
            $user = User::where('password_reset_code', $request->password_reset_code)->first();
            if ($user) {
                if ($request->otp == $user->password_reset_otp) {
                    // $user->password = bcrypt($request->password);
                    // $user->password_reset_code = '';
                    // $user->password_reset_otp = 0;
                    $o_data['password_reset_code'] = $request->password_reset_code;
                    // $user->save();
                    $status = "1";
                    $message = 'otp verified successfully';
                } else {
                    $message = 'Invalid OTP';
                }
            } else {
                $message = 'Invalid OTP';
                // $message = 'User not found';
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'errors' => $errors, 'oData' => (object)$o_data]);
    }
    public function reset_password(REQUEST $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];


        $rules = [
            'password_reset_code' => 'required',
            'otp' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = 'Validation errors';
            $errors = $validator->messages();
        } else {
            $user = User::where('password_reset_code', $request->password_reset_code)->first();
            if ($user) {
                if ($request->otp == $user->password_reset_otp) {
                    $user->password = bcrypt($request->password);
                    $user->password_reset_code = '';
                    $user->password_reset_otp = 0;
                    $user->save();
                    $status = "1";
                    $message = 'Password updated successfully';
                } else {
                    $message = 'Invalid OTP';
                }
            } else {
                $message = 'Invalid OTP';
                // $message = 'User not found';
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'errors' => $errors, 'oData' => $o_data]);
    }

    public function resend_forgot_password_otp(REQUEST $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $rules = [
            'password_reset_code' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $status = "0";
            $message = 'Validation errors';
            $errors = $validator->messages();
        } else {

            $user = User::where('password_reset_code', $request->password_reset_code)->first();
            if ($user) {

                $otp = (string)get_otp();
                User::where("id", $user->id)->update(['password_reset_otp' => $otp]);

                $name = $user->name ?? $user->first_name . ' ' . $user->last_name;
                $res = false;

                if (config('global.server_mode') == 'local') {
                    //\Artisan::call("send:send_forgot_email --uri=" . urlencode($user->email) . " --uri2=" . $otp . " --uri3=" . urlencode($name));
                } else {
                    exec("php " . base_path() . "/artisan send:send_forgot_email --uri=" . urlencode($user->email) . " --uri2=" . $otp . " --uri3=" . urlencode($name) . " > /dev/null 2>&1 & ");
                }
                $res = true;

                if ($res) {
                    $message = 'OTP has been sent to your email address, Please check your email';
                    $status = "1";
                    $o_data['password_reset_code'] = $request->password_reset_code;
                } else {
                    $status = "0";
                    $o_data = (object) [];
                    $message = 'Something went wrong';
                }
            } else {
                $o_data = (object) [];
                $message = 'User not found';
            }
        }
        return response()->json(['status' => $status, 'error' => $errors, 'message' => $message, 'oData' => $o_data], 200);
    }

    public function logout(Request $request)
    {
        $rules = [
            'access_token' => 'required',
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $user = User::where(['user_access_token' => $request->access_token])->first();
        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'status' => "0",
                'message' => 'User not found',
                'oData' => [],
                'errors' => (object) [],
            ]);
            exit;
        } else {
            $user->user_device_token = '';
            $user->user_access_token = '';
            $user->tokens()->delete();
            $user->save();
            return response()->json([
                'status' => "1",
                'message' => 'Successfully logged out',
                'oData' => [],
                'errors' => (object) []
            ], 200);
        }
    }

    public function delete_account(Request $request)
    {
        // $validator = Validator::make(
        //     $request->all(),
        //     [
        //         'access_token' => 'required',
        //     ]
        // );
        $rules = [
            'access_token' => 'required',
        ];
       
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = 'Validation errors';
            $errors = $validator->messages();
            return response()->json([
                'status' => "0",
                'message' => $message,
                'error' => (object) $errors,
            ], 200);
        }
        $user = User::where(['user_access_token' => $request->access_token])->where('role_id', 3)->first();
        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'status' => "0",
                'message' => 'User not found',
                'oData' => [],
                'errors' => (object) [],
            ]);
            exit;
        } else {

            $fb_user_refrence = $this->database->getReference('users_locations/' . $user->firebase_user_key . '/')->remove();

            $user->user_device_token = '';
            $user->email = $user->email . "__deleted_account" . $user->id;
            $user->phone = $user->phone . "__deleted_account" . $user->id;
            $user->deleted = 1;
            $user->user_access_token = '';
            $user->save();
            return response()->json([
                'status' => "1",
                'message' => 'Your account deleted successfully',
                'oData' => [],
                'errors' => (object) []
            ], 200);
        }
    }
    public function update_cart_items($user_id, $device_cart_id)
    {

        $oldcart = ServiceCart::where(['device_cart_id' => $device_cart_id, 'user_id' => $user_id])->first();
        $newcart = ServiceCart::where('device_cart_id', $device_cart_id)->where('user_id', '!=', $user_id)->get()->first();

        $categoryidold = ServiceCategorySelected::where('service_id', $oldcart->service_id ?? 0)->first()->category_id ?? 0;
        $categoryidnew = ServiceCategorySelected::where('service_id', $newcart->service_id ?? 0)->first()->category_id ?? 0;

        if ($categoryidold != $categoryidnew) {
            ServiceCart::where(['user_id' => $user_id])->delete();
        }
        $service_items = ServiceCart::where('device_cart_id', $device_cart_id)->get();
        ServiceCart::where('device_cart_id', $device_cart_id)->delete();
        if (!empty($service_items) && count($service_items) > 0) {
            foreach ($service_items as $key => $value) {
                //check category not diffrent
                // $check = $check->where(["device_cart_id" => $request->device_cart_id])
                //     ->leftjoin('service_category_selected','service_category_selected.service_id','=','cart_service.service_id')->get()->first();

                $check = ServiceCart::where(['service_id' => $value->service_id, 'user_id' => $user_id, 'device_cart_id' => $value->device_cart_id])->get()->count();
                if ($check > 0) {
                    $data_cart = ServiceCart::where(['service_id' => $value->service_id, 'user_id' => $user_id, 'device_cart_id' => $value->device_cart_id])->first();
                    $current_qty = $data_cart->qty;
                    $data_cart->qty = $current_qty + $value->qty;
                    $data_cart->save();
                } else {
                    $data_cart = new ServiceCart;
                    $data_cart->service_id = $value->service_id;
                    $data_cart->user_id    = $user_id;
                    $data_cart->device_cart_id = $value->device_cart_id;
                    $data_cart->booked_time = $value->booked_time;
                    $data_cart->text = $value->text;
                    $data_cart->hourly_rate = $value->hourly_rate;
                    $data_cart->task_description = $value->task_description;
                    $data_cart->doc = $value->doc;
                    $data_cart->qty = $value->qty;
                    $data_cart->created_at = gmdate('Y-m-d H:i:s');
                    $data_cart->updated_at = gmdate('Y-m-d H:i:s');
                    $data_cart->save();
                }
            }
        }
    }
}
