<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Carbon\Carbon;

class CountryController extends Controller
{
    //
    public function index(){
        $page_heading = "Country";
        $mode="List";
        return view('admin.country.list',compact('mode','page_heading'));
    }

    public function create($id=''){
        $page_heading = 'Country';
        $mode="Create";
        $country_name  = '';
        $country_status= '';
        $iso_code      = '';
        $dial_code     = '';
        $permissions= [];
        $route_back = route('countries.list');

        if($id){
            $page_heading = "Country";
            $mode ="Edit";
            $id = decrypt($id);
            $role = Country::find($id);
            $country_name = $role->country_name;
            $country_status = $role->country_status;
            $iso_code  = $role->iso_code;
            $dial_code = $role->dial_code;
        }
        return view('admin.country.create',compact('mode','page_heading','id','country_name','country_status','dial_code','iso_code','route_back'));

    }

    public function submit(REQUEST $request){
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('countries.list');
        $rules = [
            'country_name' => 'required',
            'dial_code' => 'required',
            'iso_code' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $country_name  = $request->country_name;
            $country_status= $request->country_status;
            $iso_code      = $request->iso_code;
            $dial_code     = $request->dial_code;
            $id         = $request->id;
            $check      = Country::whereRaw('Lower(country_name) = ?',[strtolower($country_name)])->where('country_id','!=',$id)->get();
            if($check->count() > 0){
                $message = "Country Already Addded";
                $errors['country_name'] = 'Country Already Added';
            }else{
                if($id){
                    DB::beginTransaction();
                    try{
                        $role   = Country::find($id);
                        $role->country_name    = $country_name;
                        $role->country_status  = $country_status;
                        $role->dial_code       = $dial_code;
                        $role->iso_code        = $iso_code;
                        $role->save();
                        $role_id            = $role->country_id;


                        DB::commit();
                        $status = "1";
                        $message = "Country updated Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to update country ".$e->getMessage();
                    }
                }else{
                    DB::beginTransaction();
                    try{
                        $role   = new Country();
                        $role->country_name    = $country_name;
                        $role->country_status  = $country_status;
                        $role->dial_code       = $dial_code;
                        $role->iso_code        = $iso_code;
                        $role->lang_code       = 'en';
                        $role->save();
                        $role_id            = $role->country_id;


                        DB::commit();
                        $status = "1";
                        $message = "Country Added Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to create country ".$e->getMessage();
                    }
                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function getcountryList(Request $request)
    {
        // Initialize the query for country data
        $sqlBuilder = Country::select([
            DB::raw('country_id::text as id'),
            DB::raw('country_name::text as country_name'),     
            DB::raw('iso_code::text as iso_code'),             
            DB::raw('dial_code::text as dial_code'),           
            DB::raw('country_status::text as country_status'),
            DB::raw('created_at::text as created_at'),
            DB::raw('country_id::text as country_id')
        ])
        ->orderBy('country_name', 'ASC');
    
        // Custom filtering for search (manual implementation of case-insensitive search)
       // dd($request->input('search')['value']);
        if ($request->has('search') && $request->input('search')['value'] != '') {
            $searchValue = strtolower($request->input('search')['value']); // Convert search term to lowercase
    
            
            $sqlBuilder->orwhereRaw('country_name ILIKE ?', ["%{$searchValue}%"])
                      ->orWhereRaw('iso_code ILIKE ?', ["%{$searchValue}%"])
                      ->orWhereRaw('dial_code ILIKE ?', ["%{$searchValue}%"]);
            
        }
    
        // Initialize Datatables
        return DataTables::of($sqlBuilder)
        ->editColumn('created_at', function($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        })
    
        // Handle the 'country_status' field
        ->editColumn('country_status', function($data) {
            $checked = ($data["country_status"] == 1) ? "checked" : "";
            return '<label class="switch s-icons s-outline s-outline-warning mb-4 mr-2">
                    <input type="checkbox" data-role="active-switch"
                        data-href="'.route('countries.status_change', ['id' => encrypt($data['country_id'])]).'"
                        '.$checked.' >
                    <span class="slider round"></span>
                    </label>';
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
                        href="'.route('countries.edit', ['id' => encrypt($data['country_id'])]).'">
                        <i class="flaticon-pencil-1"></i> Edit</a>';
            }
            if (get_user_permission('countries', 'd')) {
                $html .= '<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="'.route('countries.delete', ['id' => encrypt($data['country_id'])]).'">
                        <i class="flaticon-delete-1"></i> Delete</a>';
            }
            
            $html .= '</div></div>';
            return $html;
        })
    
        // Return the generated data
        ->rawColumns(['country_status', 'action'])
            ->make(true);
    }
    


    
    
    
    

    public function change_status(REQUEST $request,$id){
        $status = "0";
        $message = "";
        $o_data  = [];
        $errors = [];

        $id = decrypt($id);

        $item = Country::where(['country_id'=>$id])->get();
        if($item->count() > 0){
            $item=$item->first();
            Country::where('country_id','=',$id)->update(['country_status'=>$request->status]);
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

        $category_data = Country::where(['country_id' => $id])->first();

        if( $category_data ) {
            Country::where(['country_id' => $id])->delete();
            $message = "Country deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid Country data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
