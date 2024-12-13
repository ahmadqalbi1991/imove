<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Care;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class CareController extends Controller
{
    //
    public function index(REQUEST $request) {
        $page_heading = "Cares";
        $mode="List";
        return view('admin.care.list',compact('page_heading','mode'));
    }

    public function create()
    {
        $page_heading = "Care Create";
        $mode = "create";
        $id = "";
        $name = "";
        $icon = "";
        $status = "1";
        $route_back = route('cares.list');
        return view("admin.care.create", compact('page_heading', 'mode', 'id', 'name', 'icon', 'status','route_back'));
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

            $check_exist = Care::where(['name' => $request->name])->where('id', '!=', $request->id)->get()->toArray();
            if (empty($check_exist)) {
                $ins = [
                    'name' => $request->name,
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                    'status' => $request->status,
                ];

                if($request->file("icon")){
                    $response = image_upload($request,'cares','icon');
                    if($response['status']){
                        $ins['icon'] = $response['link'];
                    }
                }

                if ($request->id != "") {
                    $care = Care::find($request->id);
                    $care->update($ins);

                    $status = "1";
                    $message = "Care updated succesfully";
                } else {
                    $ins['created_at'] = gmdate('Y-m-d H:i:s');
                    $care = Care::create($ins);

                    $status = "1";
                    $message = "Care added successfully";
                }
            } else {
                $status = "0";
                $message = "Care Name should be unique";
                $errors['name'] = $request->name . " already added";
            }

        }
        echo json_encode(['status' => $status, 'message' => $message, 'errors' => $errors]);
    }

    public function edit($id)
    {

        $id = decrypt($id);
        $datamain = Care::find($id);

        if ($datamain) {
            $page_heading = "Care Edit";
            $mode = "edit";
            $id = $datamain->id;
            $name = $datamain->name;
            $icon = $datamain->icon;
            $status = $datamain->status;

        return view("admin.care.create", compact('page_heading', 'mode', 'id', 'name', 'icon', 'status'));
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
        $care = Care::find($id);
        if ($care) {
            $care->delete();
             $status = "1";
            $message = "Care removed successfully";
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
        if (Care::where('id', $id)->update(['status' => $request->status == '1'?'active':'inactive'])) {
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

    public function getCareList(Request $request){

        $query = Care::query()
            ->select([
                'name',
                'status',
                'created_at',
                'id',
                'icon',
            ])->orderBy('cares.id','DESC');

        if ($searchValue = $request->input('search.value')) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'ILIKE', '%' . $searchValue . '%');
            });
        }

        return DataTables::of($query)
            ->editColumn('created_at', function ($data) {
                return (new Carbon($data->created_at))->format('d/m/y H:i A');
            })
            ->editColumn('icon', function ($data) {
                return '<img src="' .$data->icon . '" width="100">';
            })
            ->editColumn('status', function ($data) {
                $checked = ($data->status == 'active') ? "checked" : "";
                return '<label class="switch s-icons s-outline s-outline-warning mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="' . route('cares.change_status', ['id' => encrypt($data->id)]) . '"
                            ' . $checked . '>
                        <span class="slider round"></span>
                    </label>';
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="dropdown custom-dropdown">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <i class="flaticon-dot-three"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                if (get_user_permission('cares', 'u')) {
                    $html .= '<a class="dropdown-item"
                           href="' . route('cares.edit', ['id' => encrypt($data->id)]) . '"><i
                               class="flaticon-pencil-1"></i> Edit</a>';
                }
                $html .= '</div></div>';
                return $html;
            })
            ->rawColumns(['icon', 'status', 'action'])
            ->make(true);
    }


    public function delete(REQUEST $request,$id) {
        $status = "0";
        $message = "";


        $id = decrypt( $id );

        $care_data = Care::where(['care_id' => $id])->first();

        if( $care_data ) {
            Care::where(['care_id' => $id])->delete();
            $message = "care deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid care data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
