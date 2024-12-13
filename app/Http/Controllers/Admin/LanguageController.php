<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Languages;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;

class LanguageController extends Controller
{
    //
    public function index(){
        $page_heading = "Languages";
        $mode="List";
        return view('admin.language.list',compact('mode','page_heading'));
    }

    public function create($id=''){
        $page_heading = 'Language';
        $mode="Create";
        $language_name  = '';
        $language_status= '';


        if($id){
            $mode= "Edit";
            $id = decrypt($id);
            $role = Languages::find($id);
            $language_name = $role->language_name;
            $language_status = $role->language_status;
        }
        return view('admin.language.create',compact('mode','page_heading','id','language_name','language_status'));

    }

    public function submit(REQUEST $request){
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('languages.list');
        $rules = [
            'language_name' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $language_name  = $request->language_name;
            $language_status= $request->language_status;
            $id         = $request->id;
            $check      = Languages::whereRaw('Lower(language_name) = ?',[strtolower($language_name)])->where('language_id','!=',$id)->get();
            if($check->count() > 0){
                $message = "Language Already Addded";
                $errors['language_name'] = 'Language Already Added';
            }else{
                if($id){
                    DB::beginTransaction();
                    try{
                        $role   = Languages::find($id);
                        $role->language_name    = $language_name;
                        $role->language_status  = $language_status;
                        $role->save();
                        $role_id            = $role->language_id;


                        DB::commit();
                        $status = "1";
                        $message = "Language updated Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to update language ".$e->getMessage();
                    }
                }else{
                    DB::beginTransaction();
                    try{
                        $role   = new Languages();
                        $role->language_name    = $language_name;
                        $role->language_status  = $language_status;
                        $role->lang_code       = 'en';
                        $role->save();
                        $role_id            = $role->language_id;


                        DB::commit();
                        $status = "1";
                        $message = "Language Added Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to create language ".$e->getMessage();
                    }
                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function getlanguageList(Request $request){

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = Languages::select([
            DB::raw('language_name::text as language_name'),
            DB::raw('language_status::text as language_status'),
            DB::raw('created_at::text as created_at'),
            DB::raw('language_id::text as language_id')
        ]);
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);



        $dt->edit('created_at',function($data){
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('language_status',function($data){
            $checked = ($data["language_status"]==1)?"checked":"";
            $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                <input type="checkbox" data-role="active-switch"
                    data-href="'.route('languages.status_change', ['id' => encrypt($data['language_id'])]).'"
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
                    if(get_user_permission('languages','u')){
                    $html.='<a class="dropdown-item"
                        href="'.route('languages.edit',['id'=>encrypt($data['language_id'])]).'"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
                    }
                    if(get_user_permission('languages','d')){
                    $html.='<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="'.route('languages.delete',['id'=>encrypt($data['language_id'])]).'"><i
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

        $item = Languages::where(['language_id'=>$id])->get();
        if($item->count() > 0){
            $item=$item->first();
            Languages::where('language_id','=',$id)->update(['language_status'=>$request->status]);
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

        $category_data = Languages::where(['language_id' => $id])->first();

        if( $category_data ) {
            Languages::where(['language_id' => $id])->delete();
            $message = "Language deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid Language data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
