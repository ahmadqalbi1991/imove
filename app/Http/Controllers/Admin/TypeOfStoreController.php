<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TypeOfStores;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;

class TypeOfStoreController extends Controller
{
    //
    public function index(){
        $page_heading = "Type Of Stores";
        return view('admin.type_of_store.list',compact('page_heading'));
    }

    public function create($id=''){
        $page_heading = 'Create Type Of Type Of Store';
        $store_name  = '';
        $store_status= '';
        

        if($id){
            $page_heading = "Edit Type Of Type Of Store";
            $id = decrypt($id);
            $role = TypeOfStores::find($id);
            $store_name = $role->store_name;
            $store_status = $role->store_status;
        }
        return view('admin.type_of_store.create',compact('page_heading','id','store_name','store_status'));

    }

    public function submit(REQUEST $request){
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('type_of_stores.list');
        $rules = [
            'store_name' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $type_of_store_name  = $request->store_name;
            $type_of_store_status= $request->store_status;
            $id         = $request->id;
            $check      = TypeOfStores::whereRaw('Lower(store_name) = ?',[strtolower($type_of_store_name)])->where('type_of_store_id','!=',$id)->get();
            if($check->count() > 0){
                $message = "Type Of Type Of Store Already Addded";
                $errors['store_name'] = 'Type Of Type Of Store Already Added';
            }else{
                if($id){
                    DB::beginTransaction();
                    try{
                        $role   = TypeOfStores::find($id);
                        $role->store_name    = $type_of_store_name;
                        $role->store_status  = $type_of_store_status;
                        $role->updated_at   = gmdate('Y-m-d H:i:s');
                        $role->save();
                        $role_id            = $role->type_of_store_id;

                        
                        DB::commit();
                        $status = "1";
                        $message = "Type Of Type Of Store updated Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to update type_of_store ".$e->getMessage();
                    }
                }else{
                    DB::beginTransaction();
                    try{
                        $role   = new TypeOfStores();
                        $role->store_name    = $type_of_store_name;
                        $role->store_status  = $type_of_store_status;
                        $role->lang_code       = 'en';
                        $role->created_at   = gmdate('Y-m-d H:i:s');
                        $role->updated_at   = gmdate('Y-m-d H:i:s');
                        $role->save();
                        $role_id            = $role->type_of_store_id;

                        
                        DB::commit();
                        $status = "1";
                        $message = "Type Of Type Of Store Added Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to create Type Of Type Of Store ".$e->getMessage();
                    }
                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }
   
    public function gettype_of_storeList(Request $request){

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = TypeOfStores::select([
            DB::raw('store_name::text as store_name'),
            DB::raw('store_status::text as store_status'),
            DB::raw('created_at::text as created_at'),
            DB::raw('type_of_store_id::text as type_of_store_id')
        ]);
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

       

        $dt->edit('created_at',function($data){
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('store_status',function($data){
            $checked = ($data["store_status"]==1)?"checked":"";
            $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                <input type="checkbox" data-role="active-switch"
                    data-href="'.route('type_of_stores.status_change', ['id' => encrypt($data['type_of_store_id'])]).'" 
                    '.$checked.' >
                <span class="slider round"></span>
            </label>';
          return $html;
        });
        

        $dt->add('action', function($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                    if(get_user_permission('type_of_stores','u')){
                    $html.='<a class="dropdown-item"
                        href="'.route('type_of_stores.edit',['id'=>encrypt($data['type_of_store_id'])]).'"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
                    }
                    if(get_user_permission('type_of_stores','d')){
                    $html.='<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="'.route('type_of_stores.delete',['id'=>encrypt($data['type_of_store_id'])]).'"><i
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

        $item = TypeOfStores::where(['type_of_store_id'=>$id])->get();
        if($item->count() > 0){
            $item=$item->first();
            TypeOfStores::where('type_of_store_id','=',$id)->update(['store_status'=>$request->status]);
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

        $category_data = TypeOfStores::where(['type_of_store_id' => $id])->first();

        if( $category_data ) {
            TypeOfStores::where(['type_of_store_id' => $id])->delete();
            $message = "Type Of Type Of Store deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid Type Of Type Of Store data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
