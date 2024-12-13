<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Costing;
use App\Models\Size;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\DB;

class CostingController extends Controller
{
    //
    public function index(REQUEST $request)
    {
        $page_heading = "Costings";
        $mode = "List";
        return view('admin.costings.list', compact('page_heading', 'mode'));
    }

    public function create()
    {
        $page_heading = "Costing Create";
        $mode = "create";
        $id = "";
        $name = "";
        $cost = "";
        $status = "1";
        $route_back = route('costings.list');
        $categories = Category::all();
        $sizes = Size::all();
        $delivery_type = "";
        $category_id = "";
        $size_id = "";
        $delivery_type_selected = "";
        return view("admin.costings.create", compact('page_heading', 'mode', 'id', 'name', 'cost', 'status', 'route_back', 'categories', 'sizes', 'delivery_type', 'category_id', 'size_id', 'delivery_type_selected'));
    }

    public function store(Request $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
        $redirectUrl = '';
   
        $rules = [
            'category_id' => 'required|integer',
            'size_id' => 'required|integer',
            'delivery_type' => 'required',
            'cost' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) 
        {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } 
        else 
        {
            $input = $request->all();

            $check_exist = Costing::where([
                'category_id' => $request->category_id,
                'size_id' => $request->size_id,
                'delivery_type' => $request->delivery_type,
                
            ])->where('id', '!=', $request->id)->get()->toArray();
            if (empty($check_exist)) 
            {
                $ins = [
                    'category_id' => $request->category_id,
                    'size_id' => $request->size_id,
                    'delivery_type' => $request->delivery_type,
                    'cost' => $request->cost,
                    'care_id' =>0,
                    'cost' => $request->cost,
                    'status' => $request->status,
                ];

                if ($request->id != "") 
                {
                    $costing = Costing::find($request->id);
                    $costing->update($ins);

                    $status = "1";
                    $message = "Costing updated succesfully";
                } 
                else 
                {
                    $ins['created_at'] = gmdate('Y-m-d H:i:s');
                    $costing = Costing::create($ins);

                    $status = "1";
                    $message = "Costing added successfully";
                }
            } 
            else 
            {
                $status = "0";
                $message = "Costing should be unique";
                $errors['category_id'] = $request->name . " already added";
            }
        }
        echo json_encode(['status' => $status, 'message' => $message, 'errors' => $errors]);
    }

    public function edit($id)
    {

        $id = decrypt($id);
        $datamain = Costing::find($id);

        if ($datamain) 
        {
            $page_heading = "Costing Edit";
            $mode = "edit";
            $id = $datamain->id;
            $name = $datamain->name;
            $status = $datamain->status;
            $categories = Category::all();
            $sizes = Size::all();
            $category_id = $datamain->category_id;
            $size_id = $datamain->size_id;
            $delivery_type_selected = $datamain->delivery_type;
            $cost = $datamain->cost;

            return view("admin.costings.create", compact('page_heading', 'mode', 'id', 'name', 'status', 'categories', 'sizes', 'category_id', 'size_id', 'delivery_type_selected', 'cost'));
        } 
        else 
        {
            abort(404);
        }
    }

    public function destroy($id)
    {

        $status = "0";
        $message = "";
        $o_data = [];
        $id = decrypt($id);
        $care = Costing::find($id);
        if ($care) {
            $care->delete();
            $status = "1";
            $message = "Costing removed successfully";
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
        if (Costing::where('id', $id)->update(['status' => $request->status == '1' ? 'active' : 'inactive'])) {
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
    public function getCostingList(Request $request)
    {
        $searchTerm = strtolower($request->input('search.value') ?? '');

        $sqlBuilder = Costing::select([
            DB::raw('costings.id::text as id'),
            DB::raw('CAST(costings.cost AS TEXT) as cost'),
            DB::raw('costings.status::text as status'),
            DB::raw('costings.status::text as status_text'),
            DB::raw('CAST(costings.created_at AS TEXT) as created_at'),
            DB::raw('categories.name::text as category_name'),
            DB::raw('sizes.name::text as size_name'),
            DB::raw('costings.delivery_type::text as delivery_type'),
        ])
        ->leftJoin('categories', 'costings.category_id', '=', 'categories.id')
        ->leftJoin('sizes', 'costings.size_id', '=', 'sizes.id')
        ->orderBy('costings.id', 'DESC');

        if (!empty($searchTerm)) {
            $sqlBuilder->where(function ($query) use ($searchTerm) {
                $query->where('costings.delivery_type', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw('costings.status ILIKE ?', ["%$searchTerm%"])
                    ->orWhereRaw('categories.name ILIKE ?', ["%$searchTerm%"]) // Use categories.name
                    ->orWhereRaw('sizes.name ILIKE ?', ["%$searchTerm%"]);
            });
        }

        return DataTables::of($sqlBuilder)
            ->editColumn('created_at', function ($data) {
                return (new Carbon($data->created_at))->format('d/m/y H:i A');
            })
            ->editColumn('cost', function ($data) {
                return config('global.default_currency_code') . ' ' . $data->cost;
            })
            ->editColumn('status_text', function ($data) {
                return $data->status === 'active'
                    ? '<div class="ticket active"><i class="fas fa-check-circle text-success"></i> Active</div>'
                    : '<div class="ticket disabled"><i class="fas fa-times-circle text-danger"></i> Disabled</div>';
            })
            ->editColumn('status', function ($data) {
                if (get_user_permission('Costings', 'u')) {
                    $checked = $data->status === 'active' ? 'checked' : '';
                    return '<label class="switch s-icons s-outline s-outline-warning mb-4 mr-2">
                    <input type="checkbox" data-role="active-switch"
                           data-href="' . route('costings.change_status', ['id' => encrypt($data->id)]) . '"
                           ' . $checked . ' >
                    <span class="slider round"></span>
                </label>';
                } else {
                    $status = $data->status === 'active' ? 'Active' : 'InActive';
                    $class = $data->status === 'active' ? 'badge-success' : 'badge-danger';
                    return '<span class="badge ' . $class . '">' . $status . '</span>';
                }
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                   <i class="flaticon-dot-three"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                if (get_user_permission('Costings', 'u')) {
                    $html .= '<a class="dropdown-item" href="' . route('costings.edit', ['id' => encrypt($data->id)]) . '">
                   <i class="flaticon-pencil-1"></i> Edit</a>';
                }
                $html .= '</div></div>';
                return $html;
            })
            ->rawColumns(['status', 'status_text', 'action','cost'])
            ->make(true);
    }
    
    public function delete(REQUEST $request, $id)
    {
        $status = "0";
        $message = "";


        $id = decrypt($id);

        $care_data = Costing::where(['care_id' => $id])->first();

        if ($care_data) {
            Costing::where(['care_id' => $id])->delete();
            $message = "Costing deleted successfully";
            $status = "1";
        } else {
            $message = "Invalid care data";
        }

        echo json_encode([
            'status' => $status, 'message' => $message
        ]);
    }
}
