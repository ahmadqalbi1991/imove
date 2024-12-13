<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryLanguages;
use Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Carbon\Carbon;

class CategoryController extends Controller
{
    //
    public function index(REQUEST $request) {
        $page_heading = "Vehicle Categories";
        $mode="List";
        return view('admin.category.list',compact('page_heading','mode'));
    }

    public function create()
    {
        $page_heading = "Vehicle Category Create";
        $mode = "create";
        $id = "";
        $name = "";
        $icon = "";
        $status = "1";
        $route_back = route('category.list');
        return view("admin.category.create", compact('page_heading', 'mode', 'id', 'name', 'icon', 'status','route_back'));
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

            $check_exist = Category::where(['name' => $request->name])->where('id', '!=', $request->id)->get()->toArray();
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
                    $category = Category::find($request->id);
                    $category->update($ins);

                    $status = "1";
                    $message = "Vehicle Category updated succesfully";
                } else {
                    $ins['created_at'] = gmdate('Y-m-d H:i:s');
                    $category = Category::create($ins);

                    $status = "1";
                    $message = "Vehicle Category added successfully";
                }
            } else {
                $status = "0";
                $message = "Vehicle Category Name should be unique";
                $errors['name'] = $request->name . " already added";
            }

        }
        echo json_encode(['status' => $status, 'message' => $message, 'errors' => $errors]);
    }

    public function edit($id)
    {

        $id = decrypt($id);
        $datamain = Category::find($id);

        if ($datamain) {
            $page_heading = "Vehicle Category Edit";
            $mode = "edit";
            $id = $datamain->id;
            $name = $datamain->name;
            $icon = $datamain->icon;
            $status = $datamain->status;

        return view("admin.category.create", compact('page_heading', 'mode', 'id', 'name', 'icon', 'status'));
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
        $category = Category::find($id);
        if ($category) {
            $category->delete();
             $status = "1";
            $message = "Vehicle Category removed successfully";
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
        if (Category::where('id', $id)->update(['status' => $request->status == '1'?'active':'inactive'])) {
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

    public function getCategoryList(Request $request){

        $sqlBuilder = Category::select([
            DB::raw('name::text as name'),
            DB::raw('status::text as status'),
            DB::raw('status::text as status_text'),
            DB::raw('created_at::text as created_at'),
            DB::raw('id::text as id'),
            DB::raw('icon::text as icon'),
        ])->orderBy('categories.id','DESC');//
        return DataTables::of($sqlBuilder)
        ->editColumn('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        })->editColumn('icon', function ($data) {

            
            return '<img src = "'.$data['icon'].'" width = "100">';
        })

       

        ->editColumn('status',function($data){
            if(get_user_permission('categories','u')){
                $checked = ($data["status"]=='active')?"checked":"";
                    $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="'.route('categories.change_status', ['id' => encrypt($data['id'])]).'"
                            '.$checked.' >
                        <span class="slider round"></span>
                    </label>';
            }else{
                $checked = ($data["status"]=='active')?"Active":"InActive";
                $class = ($data["status"]=='active')?"badge-success":"badge-danger";
                $html = '<span class="badge '.$class.'" '.$checked.' </span>';
            }
          return $html;
        })


        ->addColumn('action', function ($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';

           if (get_user_permission('categories', 'u')) {
               $html .= '<a class="dropdown-item"
                       href="' . route('categories.edit', ['id' => encrypt($data['id'])]) . '"><i
                           class="flaticon-pencil-1"></i> Edit</a>';
            }
            // if (get_user_permission('categories', 'd')) {
            //    $html .= '<a class="dropdown-item"
            //        href="' . route('categories.destroy', ['id' => encrypt($data['id'])]) . '"><i
            //    class="bx bxs-truck"></i> Delete</a>';
            // }
            $html .= '</div>
            </div>';
            return $html;
        })

        ->rawColumns(['status', 'action','icon'])
        ->make(true);
    }


    public function delete(REQUEST $request,$id) {
        $status = "0";
        $message = "";


        $id = decrypt( $id );

        $category_data = Category::where(['category_id' => $id])->first();

        if( $category_data ) {
            Category::where(['category_id' => $id])->delete();
            $message = "Vehicle category deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid category data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
