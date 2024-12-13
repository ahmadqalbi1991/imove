<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RolePermissions;
use Illuminate\Support\Facades\Auth;

class IsAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            if(!Auth::check()){
                return redirect(route('admin.login'));
            }
            //get_user_permission
            $url = \Request::segment(2);
           
            $permission = RolePermissions::where(['user_role_id_fk'=>Auth::user()->role_id])->get()->toArray();
            $user_permissions=array_column($permission,'permissions','module_key');
            \Session::put('user_permissions', $user_permissions);
            if( get_user_permission($url,'r') ){
                return $next($request);
            }else{
                return redirect(route('admin.access_restricted'));
            }

        }catch(EXCEPTION $e){
            return redirect(route('admin.access_restricted'));
        }
    }
}
