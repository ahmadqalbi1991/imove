<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use DB;

class AuthController extends Controller
{
    //
    public function login()
    {

       
        if (Auth::check() && (Auth::user()->role_id == '1')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }
    public function check_login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        // Validate request
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
           
            if (Auth::check() && (Auth::user()->role_id == '1')) {
                $request->session()->put('user_id',Auth::user()->id);
                if($request->timezone){
                    $request->session()->put('user_timezone',$request->timezone);
                }
                return response()->json(['success' => true, 'message' => "Logged in successfully."]);
            }else if (Auth::check() && (Auth::user()->is_admin_access == 1 && Auth::user()->status == config('global.user_status_active'))) {
                $request->session()->put('user_id',Auth::user()->id);
                if($request->timezone){
                    $request->session()->put('user_timezone',$request->timezone);
                }
                return response()->json(['success' => true, 'message' => "Logged in successfully."]);
            } else {
                return response()->json(['success' => false, 'message' => "Invalid Credentials!"]);
            }

        }

        return response()->json(['success' => false, 'message' => "Invalid Credentials!"]);
    }
    public function logout(){
        session()->pull("user_id");
        Auth::logout();
        return redirect()->route('admin.login');
    }
    public function access_restricted(){
        $page_heading = "Access Restricted";
        return view('admin.access_restricted',compact('page_heading'));
    }






}
