<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deligate;
use App\Models\DeligateAttribute;
use Illuminate\Http\Request;
use App\Models\IndustryTypes;
use Validator;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;

class DeligateController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $page_heading = "Deligates";
        $datamain = Deligate::select('deligates.*')
        ->get();
        
        return view('admin.deligates.list', compact('page_heading', 'datamain'));
    }

    public function getdeligateList(){
        
        $sqlBuilder = Deligate::select([
            DB::raw('name::text as name'),
            DB::raw('status::text as status'),
            DB::raw('created_at::text as created_at'),
            DB::raw('id::text as id'),
            DB::raw('icon::text as icon'),
        ])->orderBy('deligates.id','DESC');//
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('icon', function ($data) {
            return '<img src = "'.get_uploaded_image_url( $data['icon'], 'deligates_upload_dir', 'placeholder.png' ).'" width = "100">';
        });        


        $dt->edit('status',function($data){
            if(get_user_permission('deligates','u')){
                $checked = ($data["status"]=='active')?"checked":"";
                    $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="'.route('deligates.change_status', ['id' => encrypt($data['id'])]).'"
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


        $dt->add('action', function ($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
           
           if (get_user_permission('deligates', 'u')) {
               $html .= '<a class="dropdown-item"
                       href="' . route('deligates.edit', ['id' => encrypt($data['id'])]) . '"><i
                           class="flaticon-pencil-1"></i> Edit</a>';
            }
            if (get_user_permission('deligates', 'd')) {
               $html .= '<a class="dropdown-item"
                   href="' . route('deligates.destroy', ['id' => encrypt($data['id'])]) . '"><i
               class="bx bxs-truck"></i> Delete</a>';
            }    
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function create()
    {
 
        $page_heading = "Deligate Create";
        $mode = "create";
        $id = "";
        $name = "";
        $icon = "";
        $status = "1";
        $deligate_attributes = array();

        return view("admin.deligates.create", compact('page_heading', 'mode', 'id', 'name', 'icon', 'status','deligate_attributes'));
    }


    public function store(Request $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
        $redirectUrl = '';

        $rules = [
            'name' => 'required',
            'attributes' => 'required',
        ];

        if($request->id == null || $request->id == ''){
            $rules['icon'] = 'required';
        }
        $all = $request->all();
                
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $input = $request->all();

            $check_exist = Deligate::where(['name' => $request->name])->where('id', '!=', $request->id)->get()->toArray();
            if (empty($check_exist)) {
                $ins = [
                    'name' => $request->name,
                    'slug' => str_replace(' ','-',strtolower($request->name)),
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                    'status' => $request->status,
                ];

                if($request->file("icon")){
                    $response = image_upload($request,'deligates','icon');
                    if($response['status']){
                        $ins['icon'] = $response['link'];
                    }
                }

                if ($request->id != "") {
                    $deligate = Deligate::find($request->id);
                    $deligate->update($ins);

                    if(isset($all['attributes']) && count($all['attributes']) > 0){
                        DeligateAttribute::where('deligate_id',$deligate->id)->delete();
                        foreach($all['attributes'] as $attribute){

                            DeligateAttribute::create(
                                [
                                    'deligate_id' => $deligate->id, 
                                    'details' => json_encode(deligate_attribute_values($attribute)),
                                    'name' => $attribute
                                ]
                            );    
                        }
                    }

                    $status = "1";
                    $message = "Deligate updated succesfully";
                } else {
                    $ins['created_at'] = gmdate('Y-m-d H:i:s');
                    $deligate = Deligate::create($ins);

                    if(isset($all['attributes']) && count($all['attributes']) > 0){
                        DeligateAttribute::where('deligate_id',$deligate->id)->delete();
                        foreach($all['attributes'] as $attribute){
                            
                            DeligateAttribute::create(
                                [
                                    'deligate_id' => $deligate->id, 
                                    'details' => json_encode(deligate_attribute_values($attribute)),
                                    'name' => $attribute
                                ]
                            );       
                        }
                    }
                    $status = "1";
                    $message = "Deligate added successfully";
                }
            } else {
                $status = "0";
                $message = "Deligate Name should be unique";
                $errors['name'] = $request->name . " already added";
            }

        }
        echo json_encode(['status' => $status, 'message' => $message, 'errors' => $errors]);
    }

    public function edit($id)
    {

        $id = decrypt($id);
        $datamain = Deligate::find($id);

        if ($datamain) {
            $page_heading = "Deligate Edit";
            $mode = "edit";
            $id = $datamain->id;
            $name = $datamain->name;
            $icon = $datamain->icon;
            $status = $datamain->status;
            $deligate_attributes = DeligateAttribute::where('deligate_id',$datamain->id)->pluck('name')->toArray();

        return view("admin.deligates.create", compact('page_heading', 'mode', 'id', 'name', 'icon', 'status','deligate_attributes'));
        } else {
            abort(404);
        }
    }


    public function destroy($id)
    {

        $status = "0";
        $message = "";
        $o_data = [];
        $id = decrypt($id);
        $deligate = Deligate::find($id);
        if ($deligate) {
            $deligate->delete();
             $status = "1";
            $message = "Deligate removed successfully";
        } else {
            $message = "Sorry!.. You cant do this?";
        }

        return redirect()->back();

    }
    public function change_status(Request $request)
    {   
        $status = "0";
        $message = "";

        $id = decrypt($request->id);
        if (Deligate::where('id', $id)->update(['status' => $request->status == '1'?'active':'inactive'])) {
            $status = "1";
            $msg = "Successfully activated";
            if (!$request->status) {
                $msg = "Successfully deactivated";
            }
            $message = $msg;
        } else {
            $message = "Something went wrong";
        }
        echo json_encode(['status' => $status, 'message' => $message]);
    }
}
