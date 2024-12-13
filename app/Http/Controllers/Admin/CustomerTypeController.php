<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerType;
use App\Models\RolePermissions;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;

class CustomerTypeController extends Controller
{
    //
    public function index(){
        $page_heading = "Customer Types";
        $mode="List";
        return view('admin.customer_types.list',compact('mode', 'page_heading'));
    }

    public function create($id=''){
        $page_heading = 'Customer Type';
        $mode = "Create";
        $customer_type_name  = '';
        $customer_type_status= '';
        $permissions= [];

        if($id){
            $mode = "Edit";
            $id = decrypt($id);
            $customer_type = CustomerType::find($id);
            $customer_type_name = $customer_type->name;
            $customer_type_status = $customer_type->status;
        }
        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');
        return view('admin.customer_types.create',compact('mode', 'page_heading','id','customer_type_name','customer_type_status','operations','site_modules'));

    }

    public function submit(REQUEST $request){
        
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('customer_types.list');
        $rules = [
            'customer_type_name' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $customer_type_name  = $request->customer_type_name;
            $customer_type_status= $request->customer_type_status;
            $id         = $request->id;
            $check      = CustomerType::whereRaw('Lower(name) = ?',[strtolower($customer_type_name)])->where('id','!=',$id)->get();
            
            if($check->count() > 0){
                $message = "Customer Type Already Addded";
                $errors['customer_type_name'] = 'Customer Type Already Addded';
            }else{
                if($id){

                    $customer_type   = CustomerType::find($id);
                    $customer_type->name    = $customer_type_name;
                    $customer_type->status  = $customer_type_status;
                    $customer_type->save();

                    $status = "1";
                    $message = "Customer Type Updated Successfully";
                }else{

                        $customer_type   = new CustomerType();
                        $customer_type->name    = $customer_type_name;
                        $customer_type->status  = $customer_type_status;
                        $customer_type->save();

                        $status = "1";
                        $message = "Customer Type Addded Successfully";

                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


    public function getCustomeTypeList(Request $request){

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = CustomerType::select([
            DB::raw('id::text as id'),
            DB::raw('name::text as customer_type'),
            DB::raw('status::text as status'),
            DB::raw('created_at::text as created_at')
        ]);

        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);



        $dt->edit('created_at',function($data){
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('status',function($data){
            if(get_user_permission('customer_types','u')){
                $checked = ($data["status"]=='active')?"checked":"";
                    $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="'.route('customer_types.status_change', ['id' => encrypt($data['id'])]).'"
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
                    if(get_user_permission('customer_types','u')){
                    $html.='<a class="dropdown-item"
                        href="'.route('customer_types.edit',['id'=>encrypt($data['id'])]).'"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
                    }
                    
                    if(get_user_permission('customer_types','d')){
                        $html.='<a class="dropdown-item" data-role="unlink"
                            data-message="Do you want to remove this category?"
                            href="'.route('customer_types.delete',['id'=>encrypt($data['id'])]).'"><i
                                class="flaticon-delete-1"></i> Delete</a>';
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
        $item = CustomerType::find($id);
        
        if(!empty($item)){
            $item->status = ($request->status == '1')?'active':'inactive';
            $item->save();
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

        $item = CustomerType::find($id);

        if(!empty($item)) {
            $item->deleted_at = Carbon::now();
            $item->save();
            $message = "Customer Type deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid Customer Type data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
