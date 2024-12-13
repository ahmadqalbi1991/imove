<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Carbon\Carbon;
use App\Models\UserVehicle;
use App\Models\Category;
use Validator;
use App\Models\VehicleModel;
use App\Models\Manufacturer;
use App\Models\VehicleType;

use Illuminate\Http\Request;

class UserVehicleController extends Controller
{
    //
    public function index($user_id){
        $page_heading = "Vehicle";
        $mode="List";
        
        return view('admin.user_vehicles.list',compact('user_id','mode','page_heading'));
    }
    public function create($user_id='',$id=''){
        $page_heading = 'Vehicle';
        $mode="Create";
        $vehicle_name  = '';
        $catgegory_id  = '';
        $manufacturer_id='';
        $model_id='';
        $permissions= [];
        $route_back = route('vehicles.list',$user_id);
        $model_year='';


        if($id){
            $page_heading = "Vehicle";
            $mode ="Edit";
            $id = decrypt($id);
            $role = UserVehicle::find($id);
            $vehicle_name = $role->vehicle_name;
            $catgegory_id= $role->category_id;
            $manufacturer_id=$role->manufacturer_id;
             $model_id=$role->model_id;
             $model_year=$role->model_year;
           
           
        }
        $categories = Category::all();
        $manufacturer = Manufacturer::get();
        $types = VehicleType::get();
        $model = VehicleModel::get();
        return view('admin.user_vehicles.create',compact('model_year','model_id','manufacturer_id','model','types','manufacturer','catgegory_id','categories','mode','page_heading','id','vehicle_name','user_id'));

    }

    public function submit(REQUEST $request){
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('vehicles.list',$request->user_id);
        $rules = [
            'vehicle_name' => 'required',
            'user_id' => 'required',
            'category_id'=>'required',
            'model_year'=>'required'
        ];
       

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $vehicle_name  = $request->vehicle_name;
            $user_id= $request->user_id;
            $id         = $request->id;
            $category_id=$request->category_id;
            $manufacturer_id=  $request->manufacturer;
            $model_id = $request->model;
            $model_year=$request->model_year;
                if($id){
                    DB::beginTransaction();
                    try{
                        $role   = UserVehicle::find($id);
                        $role->vehicle_name    = $vehicle_name;
                        $role->user_id  = $user_id;
                        $role->category_id=$category_id;
                        $role->manufacturer_id=$manufacturer_id;
                        $role->model_id=$model_id;
                        $role->model_year=$model_year;
                        $role->save();
                       
                        
                        DB::commit();
                        $status = "1";
                        $message = "Vehicle updated Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to update country ".$e->getMessage();
                    }
                }else{
                    DB::beginTransaction();
                    try{
                        $role   = new UserVehicle();
                        $role->vehicle_name    = $vehicle_name;
                        $role->user_id  = $user_id;
                        $role->category_id=$category_id;
                        $role->manufacturer_id=$manufacturer_id;
                        $role->model_id=$model_id;
                        $role->model_year=$model_year;
                        $role->save();
                        $role_id            = $role->country_id;


                        DB::commit();
                        $status = "1";
                        $message = "Vehicle Added Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to create country ".$e->getMessage();
                    }
                }
            
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function getvehicleList($user_id,Request $request)
    {
        // Initialize the query for country data
        $sqlBuilder = UserVehicle::select([
            DB::raw('id::text as id'),
            DB::raw('vehicle_name::text as vehicle_name'),
            DB::raw('user_id::text as user_id'), 
        ])
        ->where('user_id',$user_id)
        ->orderBy('vehicle_name', 'ASC');
    
        // Custom filtering for search (manual implementation of case-insensitive search)
       // dd($request->input('search')['value']);
    
        // Initialize Datatables
        return DataTables::of($sqlBuilder)
        ->editColumn('created_at', function($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        })
    
        
    
        // Handle action buttons
        ->addColumn('action', function($data) {
            $html = '<div class="dropdown custom-dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="flaticon-dot-three"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
            
            if (get_user_permission('countries', 'u')) {
                
                $html .= '<a class="dropdown-item"
                        href="'.route('vehicles.edit', ['user_id'=>$data['user_id'],'id' => encrypt($data['id'])]).'">
                        <i class="flaticon-pencil-1"></i> Edit</a>';
            }
            if (get_user_permission('countries', 'd')) {
                $html .= '<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="'.route('vehicles.delete', ['id' => encrypt($data['id'])]).'">
                        <i class="flaticon-delete-1"></i> Delete</a>';
            }
            
            $html .= '</div></div>';
            return $html;
        })
    
        // Return the generated data
        ->rawColumns(['country_status', 'action'])
            ->make(true);
    }
    


    
    
    
    

    

    public function delete(REQUEST $request,$id) {
        $status = "0";
        $message = "";


        $id = decrypt( $id );

        $category_data = UserVehicle::where(['country_id' => $id])->first();

        if( $category_data ) {
            UserVehicle::where(['id' => $id])->delete();
            $message = "Vehicle deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid Vehicle data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
