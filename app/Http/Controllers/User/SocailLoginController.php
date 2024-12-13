<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Socialite;

class SocailLoginController extends Controller
{
    //
    public function userLogin()
    {
        // if (Auth::check() && (Auth::user()->role_id == '0')) {
        //     return redirect()->route('admin.dashboard');
        // }
        return view('user.login');
    }




    protected function _registerOrLoginUser($data){
        $user = User::where('email',$data->email)->first();
          if(!$user){
             $user = new User();
             $user->name = $data->name;
             $user->email = $data->email;
             $user->provider_id = $data->id;
             $user->avatar = $data->avatar;
             $user->save();
          }
        Auth::login($user);
        }



        //Google Login
    public function redirectToGoogle(){
    return Socialite::driver('google')->redirect();
    }
    
    //Google callback  
    public function handleGoogleCallback(){
    
    $user = Socialite::driver('google')->stateless()->user();
    
      $this->_registerorLoginUser($user);
      return redirect()->route('admin.dashboard');
    }
    
    //Facebook Login
    public function redirectToFacebook(){
    return Socialite::driver('facebook')->stateless()->redirect();
    }
    
    //facebook callback  
    public function handleFacebookCallback(){
    
    $user = Socialite::driver('facebook')->stateless()->user();
    
      $this->_registerorLoginUser($user);
      return redirect()->route('admin.dashboard');
    }
    
    //apple Login
    public function redirectToApple(){
    return Socialite::driver('apple')->stateless()->redirect();
    }
    
    //apple callback  
    public function handleAppleCallback(){
    
    $user = Socialite::driver('apple')->stateless()->user();
    
      $this->_registerorLoginUser($user);
      return redirect()->route('admin.dashboard');
    }
}
