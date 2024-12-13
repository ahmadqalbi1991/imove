<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use DB;
use Hash;

class AuthController extends Controller
{
    
    public function reset_password($token){

        if($token == null || $token == ""){
            abort(404);
        }

       $attempt = DB::table('user_password_resets')->where('token',trim($token))->where('is_valid',1)->first();
       if(!empty($attempt)){

            return view('user.reset-password',compact('attempt'));

       }else{
          return view('user.message');  
       }

    }

    public function set_password(Request $request){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data    = [];
        $rules = [
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
            
        }
        else{

           $user = User::where('email',$request->email)->where('role_id',3)->first();
           $user->password = Hash::make($request->password);
           $user->save();

           DB::table('user_password_resets')->where('id',$request->id)->update(['is_valid' => '0']);

            if(!empty($user)){
               
                $status = true;
                $message = "Password has been reset successfully";
               
            }
            else
            {
                $status = false;
                $message = "Password could not reset!";
            }

        }

        return response()->json(['success' => $status, 'message' => $message]);
    }
    
}
