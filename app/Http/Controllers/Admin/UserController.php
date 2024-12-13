<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    //
    public function index(REQUEST $request)
    {
        $page_heading = "Users";
        $mode = "List";
        return view('admin.users.list', compact('mode', 'page_heading'));
    }

    public function getuserList(Request $request)
    {

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = User::join('roles','roles.id','=','users.role_id')->select([
            'email',
            'dial_code',
            'phone',
            'roles.role as role_name',
            'users.status as user_status',
            'users.created_at as created_at',
            DB::raw('name::text as name'),
            DB::raw('users.id::text as id')
        ])->where('role_id','!=',1)->where('is_admin_role','=',1);//
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);


        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });
        $dt->edit('phone', function ($data) {
            return "+" . $data['dial_code'] . " " . $data['phone'];
        });
        // $dt->edit('user_image', function ($data) {
        //     return "
        //     <ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>
        //         <li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-xs pull-up' aria-label='Sophia Wilkerson'  data-bs-original-title='Sophia Wilkerson'>
        //             <img class='rounded-circle' src='" . get_uploaded_image_url($data['user_image'], 'user_image_upload_dir') . "' style='width:50px; height:50px;'>
        //         </li>
        //     </ul>";
        // });

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


        $dt->add('action', function ($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
            // if (get_user_permission('users', 'v')) {
            //     $html .= '<a class="dropdown-item"
            //             href="' . route('users.view', ['id' => encrypt($data['id'])]) . '"><i
            //                 class="bx bx-file"></i> View</a>';
            // }
           if (get_user_permission('users', 'u')) {
               $html .= '<a class="dropdown-item"
                       href="' . route('users.edit', ['id' => encrypt($data['id'])]) . '"><i
                           class="flaticon-pencil-1"></i> Edit</a>';
           }

            if (get_user_permission('users', 'd')) {
                $html .= '<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this user?"
                        href="' . route('users.delete', ['id' => encrypt($data['id'])]) . '"><i
                            class="flaticon-delete-1"></i> Delete</a>';
            }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }
    

    public function create(){

        $page_heading = "Create User";
        $mode = "create";
        $roles = Role::where('status','active')->where('deleted_at',null)->where('is_admin_role',1)
        ->where('id','>',1)->get();
        return view('admin.users.create',compact('roles','page_heading','mode'));

    }

    public function edit($id){
        $id = decrypt($id);
        $page_heading = "Edit User";
        $mode = "edit";
        $roles = Role::where('status','active')->where('deleted_at',null)->where('is_admin_role',1)
        ->where('id','>',1)->get();
        $user = User::find($id);
        
        return view('admin.users.edit',compact('roles','user','page_heading','mode'));

    }

    function submit(Request $request){
        
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('users.list');

        $rules = [
            'role'      => 'required',
            'name'      => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required',
            'dial_code' => 'required',
            'phone'     => 'required|unique:users',
            'status'    => 'required',
            'address'   => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',

        ];

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
            
        }
        else{

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->dial_code = $request->dial_code;
            $user->phone = $request->phone;
            $user->phone_verified = 1;
            $user->role_id = $request->role;
            $user->email_verified_at = Carbon::now();
            $user->status = ($request->status == '1'?'active':'inactive');
            $user->address = $request->address;
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->is_admin_access = 1;

            $user->save();
            
            if($user){
                $status = "1";
                $message = "User account created successfully";
            }

        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


    function update(Request $request){
       
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('users.list');

        $rules = [
            'role'      => 'required',
            'name'      => 'required',
            'email' => 'required|unique:users,email,'.$request->id,
            'dial_code' => 'required',
            'phone' => 'required|unique:users,phone,'.$request->id,
            'status'    => 'required',
            'address'   => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',

        ];

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
            
        }
        else{

            $user = User::find($request->id);
            $user->name = $request->name;
            $user->email = $request->email;

            if($request->password != null){
                $user->password = Hash::make($request->password);
            }

            $user->dial_code = $request->dial_code;
            $user->phone = $request->phone;
            $user->phone_verified = 1;
            $user->role_id = $request->role;
            $user->email_verified_at = Carbon::now();
            $user->status = ($request->status == '1'?'active':'inactive');
            $user->address = $request->address;
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->is_admin_access = 1;

            $user->save();
            
            if($user){
                $status = "1";
                $message = "User account updated successfully";
            }

        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function change_status(REQUEST $request, $id)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $id = decrypt($id);
        
        $item = User::where(['id' => $id])->get();
        if ($item->count() > 0) {
            $item = $item->first();
            User::where('id', '=', $id)->update(['status' => $request->status == '1'?'active':'inactive']);
            $status = "1";
            $message = "Status changed successfully";
        } else {
            $message = "Failed to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function delete($id)
    {
        
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $id = decrypt($id);

        $item = User::where(['id' => $id])->get();
        if ($item->count() > 0) {
            $item = $item->first();
            User::where('id', '=', $id)->delete();
            $status = "1";
            $message = "User deleted successfully";
        } else {
            $message = "Failed to delete user";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    function view($id){
        $id = decrypt($id);
        $page_heading = "User";
        $mode = "Information";
        $user = User::findOrFail($id);
        return view('admin.users.view', compact('mode', 'page_heading','user'));
    }
}
