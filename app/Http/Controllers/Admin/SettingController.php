<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    function index(){
        $page_heading = "Change Password";
        return view('admin.settings.change-password',compact('page_heading'));
    }

    function changePassword(Request $request){
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
//        $o_data['redirect'] = route('cms.pages.list');
        $rules = [
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:new_password'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occurred";
            $errors = $validator->messages();
        }
        else {
            if($request->current_password == $request->new_password){
                $status = "0";
                $message = "New password should not be same as current password";
                $errors = ['new_password' => ['New password should not be same as current password']];
            }
            else{
                $user = auth()->user();
                if(Hash::check($request->current_password, $user->password)){
                    $user->password = Hash::make($request->new_password);
                    $user->save();
                    $status = "1";
                    $message = "Password changed successfully";
                }
                else{
                    $status = "0";
                    $message = "Current password is incorrect";
                    $errors = ['current_password' => ['Current password is incorrect']];
                }
            }
        }
        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }
}
