<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryLanguages;
use App\Models\Size;
use Validator;
use Illuminate\Support\Facades\Auth;

use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class SizeController extends Controller
{
    //
    public function index(REQUEST $request) {
        $page_heading = "Sizes";
        $mode="List";
        return view('admin.sizes.list',compact('page_heading','mode'));
    }

    public function create()
    {
        $page_heading = "Size Create";
        $mode = "create";
        $id = "";
        $name = "";
        $icon = "";
        $status = "1";
        $route_back = route('sizes.list');
        return view("admin.sizes.create", compact('page_heading', 'mode', 'id', 'name', 'icon', 'status','route_back'));
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
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $input = $request->all();

            $check_exist = Size::where(['name' => $request->name])->where('id', '!=', $request->id)->get()->toArray();
            if (empty($check_exist)) {
                $ins = [
                    'name' => $request->name,
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                    'status' => $request->status,
                ];

                if($request->file("icon")){
                    $response = image_upload($request,'category','icon');
                    if($response['status']){
                        $ins['icon'] = $response['link'];
                    }
                }

                if ($request->id != "") {
                    $size = Size::find($request->id);
                    $size->update($ins);

                    $status = "1";
                    $message = "Size updated succesfully";
                } else {
                    $ins['created_at'] = gmdate('Y-m-d H:i:s');
                    $size = Size::create($ins);

                    $status = "1";
                    $message = "Size added successfully";
                }
            } else {
                $status = "0";
                $message = "Size Name should be unique";
                $errors['name'] = $request->name . " already added";
            }

        }
        echo json_encode(['status' => $status, 'message' => $message, 'errors' => $errors]);
    }

    public function edit($id)
    {

        $id = decrypt($id);
        $datamain = Size::find($id);

        if ($datamain) {
            $page_heading = "Size Edit";
            $mode = "edit";
            $id = $datamain->id;
            $name = $datamain->name;
            $icon = $datamain->icon;
            $status = $datamain->status;

        return view("admin.sizes.create", compact('page_heading', 'mode', 'id', 'name', 'icon', 'status'));
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
        $size = Size::find($id);
        if ($size) {
            $size->delete();
             $status = "1";
            $message = "Size removed successfully";
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
        if (Size::where('id', $id)->update(['status' => $request->status == '1'?'active':'inactive'])) {
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

    public function getSizeList(Request $request){

        $sqlBuilder = Size::select([
            DB::raw('name::text as name'),
            DB::raw('status::text as status'),
            DB::raw('status::text as status_text'),
            DB::raw('created_at::text as created_at'),
            DB::raw('id::text as id'),
        ])->orderBy('sizes.id','DESC');//
        return DataTables::of($sqlBuilder)
        ->editColumn('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        })

        

        ->editColumn('status',function($data){
            if(get_user_permission('categories','u')){
                $checked = ($data["status"]=='active')?"checked":"";
                    $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="'.route('sizes.change_status', ['id' => encrypt($data['id'])]).'"
                            '.$checked.' >
                        <span class="slider round"></span>
                    </label>';
            }else{
                $checked = ($data["status"]=='active')?"Active":"InActive";
                $class = ($data["status"]=='active')?"badge-success":"badge-danger";
                $html = '<span class="badge '.$class.'" '.$checked.' </span>';
            }
          return $html;
        }) ->addColumn('action', function ($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';

           if (get_user_permission('categories', 'u')) {
               $html .= '<a class="dropdown-item"
                       href="' . route('sizes.edit', ['id' => encrypt($data['id'])]) . '"><i
                           class="flaticon-pencil-1"></i> Edit</a>';
            }
            // if (get_user_permission('categories', 'd')) {
            //    $html .= '<a class="dropdown-item"
            //        href="' . route('sizes.destroy', ['id' => encrypt($data['id'])]) . '"><i
            //    class="bx bxs-truck"></i> Delete</a>';
            // }
            $html .= '</div>
            </div>';
            return $html;
        })->rawColumns(['status', 'action'])
        ->make(true);
    }


    public function delete(REQUEST $request,$id) {
        $status = "0";
        $message = "";


        $id = decrypt( $id );

        $size_data = Size::where(['category_id' => $id])->first();

        if( $size_data ) {
            Size::where(['category_id' => $id])->delete();
            $message = "Size deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
