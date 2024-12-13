<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use App\Models\User;
use App\Models\Blacklist;
use App\Models\Role;
use Validator;
use DB;
use Hash;
use Carbon\Carbon;


class BlackListController extends Controller
{
    public function index(REQUEST $request)
    {
        $page_heading = "Blacklist Users";
        $mode = "List";

        $roles = Role::where('status','active')->where('is_admin_role',0)->get();
        $companies = User::join('roles', 'roles.id', '=', 'users.role_id')
        ->select([
            'email',
            'users.user_device_id',
            'roles.role as role_name',
            'roles.id as role_id',
            'users.status as user_status',
            'users.created_at as created_at',
            DB::raw('name::text as name'),
            DB::raw('users.id::text as id')
        ])->whereNotIn('users.id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id',4)->get();
        

        $drivers = User::join('roles', 'roles.id', '=', 'users.role_id')
        ->select([
            'email',
            'users.user_device_id',
            'roles.role as role_name',
            'roles.id as role_id',
            'users.status as user_status',
            'users.created_at as created_at',
            DB::raw('name::text as name'),
            DB::raw('users.id::text as id')
        ])->whereNotIn('users.id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id',2)->get();

        $customers = User::join('roles', 'roles.id', '=', 'users.role_id')
        ->select([
            'email',
            'users.user_device_id',
            'roles.role as role_name',
            'roles.id as role_id',
            'users.status as user_status',
            'users.created_at as created_at',
            DB::raw('name::text as name'),
            DB::raw('users.id::text as id')
        ])->whereNotIn('users.id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id',3)->get();

        return view('admin.blacklist.list', compact('mode', 'page_heading','roles','companies','drivers','customers'));
    }


    public function getblackList(Request $request)
    {
        $sqlBuilder = User::join('roles', 'roles.id', '=', 'users.role_id')
            ->join('blacklists', 'blacklists.user_id', '=', 'users.id')
            ->select([
                'email',
                'dial_code',
                'phone',
                'users.user_device_id',
                'roles.role as role_name',
                'roles.id as role_id',
                'users.status as user_status',
                'users.created_at as created_at',
                DB::raw('name::text as name'),
                DB::raw('users.id::text as id')
            ]);
    
        $dt = new Datatables(new LaravelAdapter);
    
        $dt->query($sqlBuilder);
    
        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });
    
        $dt->edit('phone', function ($data) {
            return "+" . $data['dial_code'] . " " . $data['phone'];
        });
    
        $dt->edit('user_status', function ($data) {
            if (get_user_permission('users', 'u')) {
                $checked = ($data["user_status"] == 'active') ? "checked" : "";
                $html = '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                            <input type="checkbox" data-role="active-switch"
                                data-href="' . route('users.status_change', ['id' => encrypt($data['id'])]) . '"
                                ' . $checked . ' >
                            <span class="slider round"></span>
                        </label>';
            } else {
                $checked = ($data["user_status"] == 'active') ? "Active" : "InActive";
                $class = ($data["user_status"] == 'active') ? "badge-success" : "badge-danger";
                $html = '<span class="badge ' . $class . '" ' . $checked . ' </span>';
            }
            return $html;
        });
    
        $dt->add('check_all', function ($data) {
            $html = '<input type="checkbox" name="ids[]" value="'.$data['id'].'" class="check_all">';
            return $html;
        });
    
        $dt->add('action', function ($data) {
            $html = '<div class="dropdown custom-dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="flaticon-dot-three"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
    
            if ($data['role_id'] == 2) {
                if (get_user_permission('drivers', 'v')) {
                    $html .= '<a class="dropdown-item"
                                    href="' . route('drivers.view', ['id' => encrypt($data['id'])]) . '"><i
                                        class="bx bx-file"></i> View</a>';
                }
                if (get_user_permission('drivers', 'u')) {
                    $html .= '<a class="dropdown-item"
                                   href="' . route('drivers.edit', ['id' => encrypt($data['id'])]) . '"><i
                                       class="flaticon-pencil-1"></i> Edit</a>';
                }
            } elseif ($data['role_id'] == 3) {
                if (get_user_permission('customers', 'u')) {
                    $html .= '<a class="dropdown-item"
                                href="' . route('customer.edit.data', ['id' => encrypt($data['id'])]) . '"><i
                                    class="bx bx-edit"></i> edit</a>';
                }
            } elseif ($data['role_id'] == 4) {
                if (get_user_permission('company', 'u')) {
                    $html .= '<a class="dropdown-item"
                                href="' . route('company.edit', ['id' => encrypt($data['id'])]) . '"><i
                                    class="flaticon-pencil-1"></i> Edit</a>';
                }
                if (get_user_permission('company', 'd')) {
                    $html .= '<a class="dropdown-item" data-role="unlink"
                                data-message="Do you want to remove this category?"
                                href="' . route('company.delete', ['id' => encrypt($data['id'])]) . '"><i
                                    class="flaticon-delete-1"></i> Delete</a>';
                }
            }
    
            if (get_user_permission('blacklists', 'u')) {
                $html .= '<a class="dropdown-item"
                                href="' . route('blacklists.remove', ['id' => encrypt($data['id'])]) . '"><i class="fas fa-user-check"></i> BlackList</a>';
            }
    
            $html .= '</div>
                </div>';
            return $html;
        });
    
        return $dt->generate();
    }


    public function add($id){

        $id = decrypt($id);
        $user = User::find($id);
        
        if(empty($user)){
            abort(404);
        }

        $user->status = 'inactive';
        $user->save();

        $bool = BlackList::updateOrCreate(
            ['user_id' => $user->id],
            ['user_device_id' => $user->user_device_id]
        );

        $role = 'User';
        if($user->role_id == 2){
            $role = 'Drvier';
        }
        elseif($user->role_id == 3){
            $role = 'Customer';
        }
        elseif($user->role_id == 4){
            $role = 'Company';
        }

        if(!empty($bool)){
            session()->flash('success',$role.' Added Successfully to Blacklist');
        }else{
            session()->flash('error',$role.' Could Not Added to Blacklist');
        }

        return redirect()->back();


        // $startTime = $request->start_date_time;
        // $endTime = $request->end_date_time;

        // $reserved = $product->reservation_bookings()
        //     ->select('start_date_time','end_date_time','id')
        //     ->whereBetween('start_date_time', [$startTime, $endTime])
        //     ->orWhereBetween('end_date_time', [$startTime, $endTime])
        //     ->orWhere(fn ($q) => $q->where('start_date_time', '<', $startTime)->where('end_date_time', '>', $endTime))
        //     ->get();

                    //Check availability

                    // if(count($reserved) > 0){
                    //     $message = "Already Booked in these days";
                    //     return response()->json([
                    //         'status' => "0",
                    //         'message' => $message,
                    //         'errors' => (object) $errors,
                    //     ], 200);                
                    // }
    }

    public function remove($id){

        $id = decrypt($id);
        $user = User::find($id);
        
        if(empty($user)){
            abort(404);
        }

        $user->status = 'active';
        $user->save();

        $bool = BlackList::where('user_id',$user->id)->delete();

        $role = 'User';
        if($user->role_id == 2){
            $role = 'Drvier';
        }
        elseif($user->role_id == 3){
            $role = 'Customer';
        }
        elseif($user->role_id == 4){
            $role = 'Company';
        }

        if(!empty($bool)){
            session()->flash('success',$role.' Removed Successfully From Blacklist');
        }else{
            session()->flash('error',$role.' Could Not Removed From Blacklist');
        }

        return redirect()->back();

    }

    public function remove_all(Request $request){
        
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('blacklists.list');
        $rules = [
            'ids' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Please select the users";
            $errors = $validator->messages();
            
        }
        else{

            $ids = $request->ids;
            $u = User::whereIn('id',$ids)->update(['status'=>'active']);
            $b = BlackList::whereIn('user_id',$ids)->delete();

            if($u && $b){
                $status = "1";
                $message = "Users removed successfully from the blacklist";    
            }
            else{
                $status = "1";
                $message = "Users could not removed from the blacklist";
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    function add_all(Request $request){

        $users = $request->add_users;
        
        if($users != null && count($users) > 0){
            
            foreach($users as $id){

                $user = User::find($id);
                
                if(empty($user)){
                    continue;
                }

                $user->status = 'active';
                $user->save();

                $bool = BlackList::updateOrCreate(
                    ['user_id' => $user->id],
                    ['user_device_id' => $user->user_device_id]
                );
                session()->flash('success','Users Added Successfully to Blacklist');
            }
        }else{
            session()->flash('error','Users Could Not Added to Blacklist');
        }

        return redirect()->back();
    }
}
