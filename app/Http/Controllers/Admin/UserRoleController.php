<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermissions;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;

class UserRoleController extends Controller
{
    //
    public function index(){
        $page_heading = "User Roles";
        $mode="List";
        return view('admin.roles.list',compact('mode', 'page_heading'));
    }

    public function create($id=''){
        $page_heading = 'Role';
        $mode = "Create";
        $role_name  = '';
        $is_admin_role  = '';
        $status= '';
        $permissions= [];

        if($id){
            $mode = "Edit";
            $id = decrypt($id);
            $role = Role::find($id);
            $role_name = $role->role;
            $status = $role->status;
            $is_admin_role = $role->is_admin_role;
            $permission = RolePermissions::where('user_role_id_fk','=',$id)->get()->toArray();
            $permissions = array_column($permission,'permissions','module_key');
        }
        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');
        return view('admin.roles.create',compact('mode', 'page_heading','id','status','role_name','is_admin_role','permissions','operations','site_modules'));

    }

    public function submit(REQUEST $request){
        
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('user_roles.list');
        $rules = [
            'role' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $permission = $request->permission;
            $role_name  = $request->role;
            $status= $request->status;
            $is_admin_role= (integer) $request->is_admin_role;
            $id         = $request->id;
            $check      = Role::whereRaw('Lower(role) = ?',[strtolower($role_name)])->where('id','!=',$id)->get();
           
            if($check->count() > 0){
                $message = "Role Already Addded";
                $errors['role'] = 'Role Already Added';
            }else{
                if($id){
                    DB::beginTransaction();
                    try{
                        $role   = Role::find($id);
                        $role->role    = $role_name;
                        $role->status  = $status;
                        $role->is_admin_role  = $is_admin_role;
                        $role->save();
                        $role_id            = $role->id;

                        RolePermissions::where(['user_role_id_fk'=>$role_id])->delete();
                        $module_permissions = [];
                        $site_modules = config('crud.site_modules');
                        foreach($site_modules as $moduleKey=>$moduleName){
                            if( isset($permission[$moduleKey]) ){
                                $module_permissions[]= [
                                    'module_key'        => $moduleKey,
                                    'user_role_id_fk'   => $role_id,
                                    'permissions'       => json_encode($permission[$moduleKey]??[])
                                ];
                            }
                        }
                        if(!empty($module_permissions)){
                            RolePermissions::insert($module_permissions);
                        }
                        DB::commit();
                        $status = "1";
                        $message = "Role Permissions updated Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Failed to create variation ".$e->getMessage();
                    }
                }else{
                    DB::beginTransaction();
                    try{
                        $role   = new Role();
                        $role->role    = $role_name;
                        $role->status  = $status;
                        $role->is_admin_role  = $is_admin_role;
                        $role->save();
                        $role_id            = $role->id;

                        $module_permissions = [];
                        $site_modules = config('crud.site_modules');
                        foreach($site_modules as $moduleKey=>$moduleName){
                            if( isset($permission[$moduleKey]) ){
                                $module_permissions[]= [
                                    'module_key'        => $moduleKey,
                                    'user_role_id_fk'   => $role_id,
                                    'permissions'       => json_encode($permission[$moduleKey]??[])
                                ];
                            }
                        }
                        if(!empty($module_permissions)){
                            RolePermissions::insert($module_permissions);
                        }
                        DB::commit();
                        $status = "1";
                        $message = "Role Permissions Added Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to create variation ".$e->getMessage();
                    }
                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function getroleList(Request $request){

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = Role::select([
            DB::raw('role::text as role'),
            DB::raw('status::text as status'),
            DB::raw('is_admin_role::text as is_admin_role'),
            DB::raw('created_at::text as created_at'),
            DB::raw('id::text as id')
        ])->where('id','!=',1)->where('is_admin_role','=',1);
        
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('created_at',function($data){
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('status',function($data){
            if(get_user_permission('user_roles','u')){
                $checked = ($data["status"]=='active')?"checked":"";
                    $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="'.route('user_roles.status_change', ['id' => encrypt($data['id'])]).'"
                            '.$checked.' >
                        <span class="slider round"></span>
                    </label>';
            }else{
                $checked = ($data["status"]=='active')?"Active":"InActive";
                $class = ($data["status"]=='active')?"badge-success":"badge-danger";
                $html = '<span class="badge '.$class.'" '.$checked.' </span>';
            }
          return $html;
        });


        $dt->add('action', function($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                    if(get_user_permission('user_roles','u')){
                    $html.='<a class="dropdown-item"
                        href="'.route('user_roles.edit',['id'=>encrypt($data['id'])]).'"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
                    }
                    
                    if($data['is_admin_role'] == '1'){

                        if(get_user_permission('user_roles','d')){
                            $html.='<a class="dropdown-item" data-role="unlink"
                                data-message="Do you want to remove the role? Make sure All users will be removed related to this role!"
                                href="'.route('user_roles.delete',['id'=>encrypt($data['id'])]).'"><i
                                    class="flaticon-delete-1"></i> Delete</a>';
                        }                             
                    
                    }
            $html.='</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function change_status(REQUEST $request,$id){
        $status = "0";
        $message = "";
        $o_data  = [];
        $errors = [];

        $id = decrypt($id);

        $item = Role::where(['id'=>$id])->get();
 
        if($item->count() > 0){

            Role::where('id','=',$id)->update(['status'=>$request->status == '1'?'active':'inactive']);
            $status = "1";
            $message= "Status changed successfully";
        }else{
            $message = "Faild to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function delete(REQUEST $request,$id) {
        $status = "0";
        $message = "";

        $id = decrypt( $id );

        $category_data = Role::where(['id' => $id])->first();

        if( $category_data ) {
            Role::where(['id' => $id])->delete();
            $message = "Role deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid Role data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
