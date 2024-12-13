<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\TruckType;
use App\Models\DriverDetail;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;


class DriverController extends Controller
{

    public function index(REQUEST $request)
    {
        $page_heading = "Drivers";
        $mode = "List";
        return view('admin.drivers.list', compact('mode', 'page_heading'));
    }

    public function getdriversList(Request $request)
    {
        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = User::join('roles','roles.id','=','users.role_id')
        ->join('driver_details','driver_details.user_id','=','users.id')->select([
            'email',
            'dial_code',
            'phone',
            'roles.role as role_name',
            'users.status as user_status',
            DB::raw('users.created_at::text as created_at'),
            DB::raw('driver_details.total_rides::text as total_rides'),
            'driver_details.is_company as is_company',
            DB::raw('name::text as name'),
            DB::raw('users.id::text as id')
        ])->whereNotIn('user_id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id','=',2);

        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);


        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });
        $dt->edit('phone', function ($data) {
            return "+" . $data['dial_code'] . " " . $data['phone'];
        });

        $dt->edit('is_company', function ($data) {
            $type = '';
            if($data['is_company'] == 'yes'){
                $type = 'Company';
            }else{
                $type = 'Individual';
            }
            return $type;
        });
        // $dt->edit('user_image', function ($data) {
        //     return "
        //     <ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>
        //         <li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-xs pull-up' aria-label='Sophia Wilkerson'  data-bs-original-title='Sophia Wilkerson'>
        //             <img class='rounded-circle' src='" . get_uploaded_image_url($data['user_image'], 'user_image_upload_dir') . "' style='width:50px; height:50px;'>
        //         </li>
        //     </ul>";
        // });

        $dt->edit('user_status', function ($data) {
            if (get_user_permission('drivers', 'u')) {
                $checked = ($data["user_status"] == 'active') ? "checked" : "";
                $html = '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="' . route('drivers.status_change', ['id' => encrypt($data['id'])]) . '"
                            ' . $checked . ' >
                        <span class="slider round"></span>
                    </label>';
            } else {
                $checked = ($data["user_status"] == 'active') ? "Active" : "InActive";
                $class = ($data["user_status"] == 'active') ? "badge-success" : "badge-danger";
                $html = '<span class="badge ' . $class . '" ' . $checked . ' </span>';
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
                    if (get_user_permission('drivers', 'v')) {
                        $html .= '<a class="dropdown-item"
                                href="' . route('drivers.view', ['id' => encrypt($data['id'])]) . '"><i
                                    class="bx bx-file"></i> View</a>';
                    }
                if (get_user_permission('drivers', 'u')) {
                    $html .= '<a class="dropdown-item"
                            href="' . route('drivers.edit', ['id' => encrypt($data['id'])]) . '"><i
                                class="flaticon-pencil-1"></i> Edit</a>';
                }
                if (get_user_permission('drivers', 'u')) {
                    $html .= '<a class="dropdown-item"
                            href="' . route('bookings.list.pickup_orders', ['p_driver_id' =>$data['id']]) . '"><i
                                class="flaticon-pencil-1"></i> Pickup Deliveries</a>';
                }
                if (get_user_permission('drivers', 'u')) {
                    $html .= '<a class="dropdown-item"
                            href="' . route('bookings.list.delivery_orders', ['d_driver_id' =>$data['id']]) . '"><i
                                class="flaticon-pencil-1"></i> Drop off Deliveries </a>';
                }
               

            // if (get_user_permission('users', 'd')) {
            //     $html .= '<a class="dropdown-item" data-role="unlink"
            //             data-message="Do you want to remove this user?"
            //             href="' . route('user_roles.delete', ['id' => encrypt($data['id'])]) . '"><i
            //                 class="flaticon-delete-1"></i> Delete</a>';
            // }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }
    

    public function create(){

        $page_heading = "Create Driver Account";
        $mode = "create";
        $companies = User::where('status','active')->where('role_id',4)->get();
        $get_driver_types = get_driver_types();
        $trucks = TruckType::where('status','active')->get();
    
        return view('admin.drivers.create',compact('companies','get_driver_types','page_heading','mode','trucks'));

    }


    function submit(Request $request){
         
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('drivers.list');
        $rules = [
            //'truck_type' => 'required',
            //'driver_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'password' => 'required',
            'dial_code' => 'required',
            'phone' => [
                'required',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'driving_license' => 'required|mimes:jpeg,png,jpg,gif',
            'emirates_id_or_passport' => 'required|mimes:jpeg,png,jpg,gif',
            'driving_license_number' => 'required|unique:driver_details',
            'driving_license_expiry' => 'required',
            'driving_license_issued_by' => 'required',
            'vehicle_plate_number' => 'required',
            'vehicle_plate_place' => 'required',
            'mulkiya' => 'required|mimes:jpeg,png,jpg,gif',
            'mulkiya_number' => 'required',
            'status' => 'required',
            'address' => 'required',
            'country' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
        if($request->driver_type == '1'){
            $rules['company'] = 'required';
        }
       
        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
            
        }
        else{

            $user = new User();
            $user->name = $request->first_name.' '.$request->last_name;;


            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->password = Hash::make($request->password);
            $user->dial_code = $request->dial_code;
            $user->phone = $request->phone;
            $user->phone_verified = 1;
            $user->role_id = 2;
            $user->email_verified_at = Carbon::now();
            $user->status = $request->status;
            $user->address = $request->address;
            $user->address_2 = $request->address_2;
            $user->country = $request->country;
            $user->city = $request->city;
            $user->zip_code = $request->zip_code;
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->save();

            if(!empty($user)){
                
                $driving_drivers = array();
                
                $driving_drivers['mulkia_number'] = $request->mulkiya_number;
                $driving_drivers['driving_license_issued_by'] = $request->driving_license_issued_by;
                $driving_drivers['driving_license_number'] = $request->driving_license_number;
                $driving_drivers['driving_license_expiry'] = date('Y-m-d',strtotime($request->driving_license_expiry));
                $driving_drivers['vehicle_plate_number'] = $request->vehicle_plate_number;
                $driving_drivers['vehicle_plate_place'] = $request->vehicle_plate_place;

                $driving_drivers['truck_type_id'] = $request->truck_type??0;
                $driving_drivers['total_rides'] = 0;
                $driving_drivers['address'] = $request->address;
                $driving_drivers['latitude'] = $request->latitude;
                $driving_drivers['longitude'] = $request->longitude;


                if($request->driver_type == '1'){
                    $driving_drivers['is_company'] = 'yes';
                    $driving_drivers['company_id'] = $request->company;
                }else{
                    $driving_drivers['company_id'] = 1;
                    $driving_drivers['is_company'] = 'no';
                }


                if($request->file("driving_license") != null){
                        $response = image_upload($request,'users','driving_license');
                        
                        if($response['status']){
                            $driving_drivers['driving_license'] = $response['link'];
                        }
                }


                if($request->file("mulkiya") != null){
                        $response = image_upload($request,'users','mulkiya');
                        
                        if($response['status']){
                            $driving_drivers['mulkia'] = $response['link'];
                        }
                }

                if($request->file("emirates_id_or_passport") != null){
                    $response = image_upload($request,'users','emirates_id_or_passport');
                    
                    if($response['status']){
                        $driving_drivers['emirates_id_or_passport'] = $response['link'];
                    }
                }
                
                $bool = DriverDetail::updateOrCreate(['user_id' => $user->id],
                                $driving_drivers
                            );
                               
                if($bool){
                        $status = "1";
                        $message = "Driver account created Successfully";
                }
                else
                {
                    $status = "0";
                    $message = "Driver account could not created";
                }               
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function edit($id){
        $id = decrypt($id);
        
        $user = User::find($id);
        if(!empty($user)){

            $page_heading = "Edit Driver Account";
            $mode = "edit";
            $companies = User::where('status','active')->where('role_id',4)->get();
            $get_driver_types = get_driver_types();
            $trucks = TruckType::where('status','active')->get();
            return view('admin.drivers.edit',compact('companies','get_driver_types','page_heading','mode','user','trucks'));

        }
        else{
            abort(404);
        } 

    }    


    function update(Request $request,$id){
        
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('drivers.list');
        $rules = [
            //'truck_type' => 'required',
            //'driver_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'dial_code' => 'required',
            'phone' => 'required|unique:users,phone,'.$id,
            'mulkiya_number' => 'required',
            'status' => 'required',
            'address' => 'required',
            'country' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'driving_license_expiry' => 'required',
            'driving_license_issued_by' => 'required',
            'vehicle_plate_number' => 'required',
            'vehicle_plate_place' => 'required',
            'driving_license_number' => 'required|unique:driver_details,driving_license_number, '.$request->driver_detail_id,

        ];
        if($request->driver_type == '1'){
            $rules['company'] = 'required';
        }

        if($request->file("driving_license") != null){
            $rules['driving_license'] = 'required|mimes:jpeg,png,jpg,gif';
        }

        if($request->file("emirates_id_or_passport") != null){
            $rules['emirates_id_or_passport'] = 'required|mimes:jpeg,png,jpg,gif';
        }

        if($request->file("mulkiya") != null){
            $rules['mulkiya'] = 'required|mimes:jpeg,png,jpg,gif';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
            
        }
        else{

            $user = User::find($id);
            $user->name = $request->first_name.' '.$request->last_name;
            $user->email = $request->email;

            if($request->password != null){
                $user->password = Hash::make($request->password);
            }

            $name = $request->first_name.' '.$request->last_name;
            
            $user->dial_code = $request->dial_code;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->phone = $request->phone;
            $user->phone_verified = 1;
            $user->role_id = 2;
            $user->email_verified_at = Carbon::now();
            $user->status = $request->status;
            $user->address = $request->address;
            $user->address_2 = $request->address_2;
            $user->country = $request->country;
            $user->city = $request->city;
            $user->zip_code = $request->zip_code;
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;

            $user->save();

            if(!empty($user)){
                
                $driving_drivers = array();             
                $driving_drivers['mulkia_number'] = $request->mulkiya_number;
                $driving_drivers['driving_license_issued_by'] = $request->driving_license_issued_by;
                $driving_drivers['driving_license_number'] = $request->driving_license_number;
                $driving_drivers['driving_license_expiry'] = date('Y-m-d',strtotime($request->driving_license_expiry));
                $driving_drivers['vehicle_plate_number'] = $request->vehicle_plate_number;
                $driving_drivers['vehicle_plate_place'] = $request->vehicle_plate_place;

                $driving_drivers['truck_type_id'] = $request->truck_type??0;
                $driving_drivers['total_rides'] = 0;
                $driving_drivers['address'] = $request->address;
                $driving_drivers['latitude'] = $request->latitude;
                $driving_drivers['longitude'] = $request->longitude;    


                if($request->driver_type == '1'){
                    $driving_drivers['is_company'] = 'yes';
                    $driving_drivers['company_id'] = $request->company;
                }else{
                    $driving_drivers['company_id'] = 0;
                    $driving_drivers['is_company'] = 'no';
                }


                if($request->file("driving_license") != null){
                        $response = image_upload($request,'users','driving_license');
                        
                        if($response['status']){
                            $driving_drivers['driving_license'] = $response['link'];
                        }
                }


                if($request->file("mulkiya") != null){
                        $response = image_upload($request,'users','mulkiya');
                        
                        if($response['status']){
                            $driving_drivers['mulkia'] = $response['link'];
                        }
                }

                if($request->file("emirates_id_or_passport") != null){
                    $response = image_upload($request,'users','emirates_id_or_passport');
                    
                    if($response['status']){
                        $driving_drivers['emirates_id_or_passport'] = $response['link'];
                    }
                }

                $bool = DriverDetail::updateOrCreate(['user_id' => $user->id],
                                $driving_drivers
                            );
                               
                if($bool){
                        $status = "1";
                        $message = "Driver account updated successfully";
                }
                else
                {
                    $status = "0";
                    $message = "Driver account could not updated";
                }               
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function change_status(REQUEST $request, $id)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $id = decrypt($id);
        $item = User::where(['id' => $id])->get();
        if ($item->count() > 0) {
            $item = $item->first();
            User::where('id', '=', $id)->update(['status' => $request->status == '1'?'active':'inactive']);
            $status = "1";
            $message = "Status changed successfully";
        } else {
            $message = "Faild to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }
    function view($id){
        $id = decrypt($id);
        
        $user = User::find($id);
        if(!empty($user)){

            $page_heading = "View Driver Account";
            $mode = "view";
            $companies = User::where('status','active')->where('role_id',4)->get();
            $get_driver_types = get_driver_types();
            $trucks = TruckType::where('status','active')->get();
            return view('admin.drivers.view',compact('companies','get_driver_types','page_heading','mode','user','trucks'));

        }
        else{
            abort(404);
        } 
    }
}
