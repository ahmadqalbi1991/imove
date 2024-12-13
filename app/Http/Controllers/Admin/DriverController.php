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
use Yajra\DataTables\Facades\DataTables;
use DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\BookingPickUpOrder;


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
        $sqlBuilder = User::join('roles', 'roles.id', '=', 'users.role_id')
            ->join('driver_details', 'driver_details.user_id', '=', 'users.id')
            ->select([
                'email',
                'dial_code',
                'phone',
                'roles.role as role_name',
                'users.status as status',
                'driver_details.is_company as my_company',  // Correctly reference the is_company column from driver_details
                DB::raw('users.created_at::text as created_at'),
                DB::raw('driver_details.total_rides::text as total_rides'),
                DB::raw('name::text as name'),
                DB::raw('users.id::text as id')
            ])
            ->whereNotIn('users.id', function ($query) {
                $query->select('user_id')
                    ->from('blacklists')
                    ->whereColumn('users.id', '=', 'blacklists.user_id');
            })
            ->where('role_id', '=', 2)
            ->where('deleted', '!=', 1)->latest();  // Ensure role_id is for drivers

        return DataTables::of($sqlBuilder)
            ->editColumn('created_at', function ($data) {
                return (new Carbon($data['created_at']))->format('d/m/y H:i A');
            })
            ->editColumn('phone', function ($data) {
                return "+" . $data['dial_code'] . " " . $data['phone'];
            })
            ->addColumn('user_status', function ($data) {
                if (get_user_permission('drivers', 'u')) {
                    $checked = ($data["status"] == 'active') ? "checked" : "";
                    $html = '<label class="switch s-icons s-outline s-outline-warning mb-4 mr-2">
                                <input type="checkbox" data-role="active-switch"
                                    data-href="' . route('drivers.status_change', ['id' => encrypt($data['id'])]) . '"
                                    ' . $checked . ' >
                                <span class="slider round"></span>
                            </label>';
                } else {
                    $checked = ($data["status"] == 'active') ? "Active" : "InActive";
                    $class = ($data["status"] == 'active') ? "badge-success" : "badge-danger";
                    $html = '<span class="badge ' . $class . '" ' . $checked . ' </span>';
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
                if (get_user_permission('drivers', 'v')) {
                    $html .= '<a class="dropdown-item" href="' . route('drivers.view', ['id' => encrypt($data['id'])]) . '"><i class="bx bx-file"></i> View</a>';
                }
                if (get_user_permission('drivers', 'u')) {
                    $html .= '<a class="dropdown-item" href="' . route('drivers.edit', ['id' => encrypt($data['id'])]) . '"><i class="flaticon-pencil-1"></i> Edit</a>';
                    $html .= '<a class="dropdown-item" href="' . route('bookings.list.new', ['p_driver_id' => $data['id']]) . '"><i class="flaticon-pencil-1"></i> Bookings</a>';
                }
                if (get_user_permission('drivers', 'd')) {
                    $html .= '<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this Driver?"
                        href="' . route('drivers.delete', ['id' => encrypt($data['id'])]) . '"><i
                            class="flaticon-delete-1"></i> Delete</a>';
                }
                $html .= '</div></div>';
                return $html;
            })
            ->rawColumns(['user_status', 'action'])
            ->make(true);
    }


    public function create()
    {

        $page_heading = "Create Driver Account";
        $mode = "create";
        $companies = User::where('status', 'active')->where('role_id', 4)->get();
        $get_driver_types = get_driver_types();
        $trucks = TruckType::where('status', 'active')->get();

        return view('admin.drivers.create', compact('companies', 'get_driver_types', 'page_heading', 'mode', 'trucks'));

    }


    function submit(Request $request)
    {

        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
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
            'mulkiya' => 'required|mimes:jpeg,png,jpg,gif',
            'mulkiya_number' => 'required',
            'status' => 'required',

            'country' => 'required',
            'city' => 'required',
            'zip_code' => 'required',

        ];

        $vehicle_types = '';
        if (!empty($request->vehicle_type)) {
            $vehicle_types = implode(',', $request->vehicle_type);
        }

        if ($request->driver_type == '1') {
            $rules['company'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();

        } else {

            $user = new User();
            $user->name = $request->first_name . ' ' . $request->last_name;


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
            $user->vehicle_type = $vehicle_types;
            $user->save();

            if (!empty($user)) {

                $driving_drivers = array();

                $driving_drivers['mulkia_number'] = $request->mulkiya_number;
                $driving_drivers['driving_license_issued_by'] = $request->driving_license_issued_by;
                $driving_drivers['driving_license_number'] = $request->driving_license_number;
                $driving_drivers['driving_license_expiry'] = date('Y-m-d', strtotime($request->driving_license_expiry));
                $driving_drivers['vehicle_plate_number'] = $request->vehicle_plate_number;
                $driving_drivers['vehicle_plate_place'] = $request->vehicle_plate_place;

                $driving_drivers['truck_type_id'] = $request->truck_type ?? 0;
                $driving_drivers['total_rides'] = 0;
                $driving_drivers['address'] = $request->address;
                $driving_drivers['latitude'] = $request->latitude;
                $driving_drivers['longitude'] = $request->longitude;


                if ($request->driver_type == '1') {
                    $driving_drivers['is_company'] = 'yes';
                    $driving_drivers['company_id'] = $request->company;
                } else {
                    $driving_drivers['company_id'] = 1;
                    $driving_drivers['is_company'] = 'no';
                }

                if ($request->file("driving_license") != null) {
                    $file = $request->file('driving_license');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $s3Path = 'imove/driving_license/' . $fileName;
                    $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
                    $driving_drivers['driving_license'] = \Storage::disk('s3')->url($s3Path);
                }


                if ($request->file("mulkiya") != null) {
                    $file = $request->file('mulkiya');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $s3Path = 'imove/mulkia/' . $fileName;
                    $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
                    $driving_drivers['mulkia'] = \Storage::disk('s3')->url($s3Path);
                }

                if ($request->file("emirates_id_or_passport") != null) {
                    $response = image_upload($request, 'users', 'emirates_id_or_passport');

                    if ($response['status']) {
                        $driving_drivers['emirates_id_or_passport'] = $response['link'];
                    }
                }

                $bool = DriverDetail::updateOrCreate(['user_id' => $user->id],
                    $driving_drivers
                );

                if ($bool) {
                    $status = "1";
                    $message = "Driver account created Successfully";
                } else {
                    $status = "0";
                    $message = "Driver account could not created";
                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $user = User::find($id);
        if (!empty($user)) {

            $page_heading = "Edit Driver Account";
            $mode = "edit";
            $companies = User::where('status', 'active')->where('role_id', 4)->get();
            $get_driver_types = get_driver_types();
            $trucks = TruckType::where('status', 'active')->get();
            return view('admin.drivers.edit', compact('companies', 'get_driver_types', 'page_heading', 'mode', 'user', 'trucks'));

        } else {
            abort(404);
        }

    }


    function update(Request $request, $id)
    {

        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
        $o_data['redirect'] = route('drivers.list');
        $rules = [
            //'truck_type' => 'required',
            //'driver_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,' . $id,
            'dial_code' => 'required',
            'phone' => 'required|unique:users,phone,' . $id,
            'mulkiya_number' => 'required',
            'status' => 'required',

            'country' => 'required',
            'city' => 'required',
            'zip_code' => 'required',

            'driving_license_expiry' => 'required',
            'driving_license_issued_by' => 'required',
            'vehicle_type' => 'required',


            'driving_license_number' => 'required|unique:driver_details,driving_license_number, ' . $request->driver_detail_id,

        ];
        $vehicle_types = '';
        if (!empty($request->vehicle_type)) {
            $vehicle_types = implode(',', $request->vehicle_type);
        }
        if ($request->driver_type == '1') {
            $rules['company'] = 'required';
        }

        if ($request->file("driving_license") != null) {
            $rules['driving_license'] = 'required|mimes:jpeg,png,jpg,gif';
        }

        if ($request->file("mulkiya") != null) {
            $rules['mulkiya'] = 'required|mimes:jpeg,png,jpg,gif';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();

        } else {

            $user = User::find($id);
            $user->name = $request->first_name . ' ' . $request->last_name;
            $user->email = $request->email;

            if ($request->password != null) {
                $user->password = Hash::make($request->password);
            }

            $name = $request->first_name . ' ' . $request->last_name;

            $user->dial_code = $request->dial_code;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->vehicle_type = $vehicle_types;
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

            if (!empty($user)) {

                $driving_drivers = array();
                $driving_drivers['mulkia_number'] = $request->mulkiya_number;
                $driving_drivers['driving_license_issued_by'] = $request->driving_license_issued_by;
                $driving_drivers['driving_license_number'] = $request->driving_license_number;
                $driving_drivers['driving_license_expiry'] = date('Y-m-d', strtotime($request->driving_license_expiry));
                $driving_drivers['vehicle_plate_number'] = $request->vehicle_plate_number;
                $driving_drivers['vehicle_plate_place'] = $request->vehicle_plate_place;

                $driving_drivers['truck_type_id'] = $request->truck_type ?? 0;
                $driving_drivers['total_rides'] = 0;
                $driving_drivers['address'] = $request->address;
                $driving_drivers['latitude'] = $request->latitude;
                $driving_drivers['longitude'] = $request->longitude;


                if ($request->driver_type == '1') {
                    $driving_drivers['is_company'] = 'yes';
                    $driving_drivers['company_id'] = $request->company;
                } else {
                    $driving_drivers['company_id'] = 0;
                    $driving_drivers['is_company'] = 'no';
                }


                if ($request->file("driving_license") != null) {
                    $file = $request->file('driving_license');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $s3Path = 'imove/driving_license/' . $fileName;
                    $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
                    $driving_drivers['driving_license'] = \Storage::disk('s3')->url($s3Path);
                }


                if ($request->file("mulkiya") != null) {
                    $file = $request->file('mulkiya');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $s3Path = 'imove/mulkia/' . $fileName;
                    $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
                    $driving_drivers['mulkia'] = \Storage::disk('s3')->url($s3Path);
                }

                if ($request->file("emirates_id_or_passport") != null) {
                    $response = image_upload($request, 'users', 'emirates_id_or_passport');

                    if ($response['status']) {
                        $driving_drivers['emirates_id_or_passport'] = $response['link'];
                    }
                }

                $bool = DriverDetail::updateOrCreate(['user_id' => $user->id],
                    $driving_drivers
                );

                if ($bool) {
                    $status = "1";
                    $message = "Driver account updated successfully";
                } else {
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
            User::where('id', '=', $id)->update(['status' => $request->status == '1' ? 'active' : 'inactive']);
            $status = "1";
            $message = "Status changed successfully";
        } else {
            $message = "Faild to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    function view($id)
    {
        $id = decrypt($id);

        $user = User::find($id);
        if (!empty($user)) {

            $page_heading = "View Driver Account";
            $mode = "view";
            $companies = User::where('status', 'active')->where('role_id', 4)->get();
            $get_driver_types = get_driver_types();
            $trucks = TruckType::where('status', 'active')->get();
            return view('admin.drivers.view', compact('companies', 'get_driver_types', 'page_heading', 'mode', 'user', 'trucks'));

        } else {
            abort(404);
        }
    }

    public function delete($id)
    {

        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $id = decrypt($id);

        $item = User::where(['id' => $id])->get();
        if ($item->count() > 0) {
            $item = $item->first();
            $user = $item;

            $datamain = BookingPickUpOrder::where(function ($query) use ($user) {
                $query->where('pickup_driver', $user->id)
                    ->orWhere('delivery_driver', $user->id);
            })->where('booking_status', '!=', 8)
                ->get();
            if ($datamain->first()) {
                $status = "0";
                $message = "Failed to delete user, Customer have some bookings";
            }
            $user->user_device_token = '';
            $user->email = $user->email . "__deleted_account" . $user->id;
            $user->phone = $user->phone . "__deleted_account" . $user->id;
            $user->deleted = 1;
            $user->user_access_token = '';
            $user->save();
            // User::where('id', '=', $id)->delete();
            $status = "1";
            $message = "User deleted successfully";
        } else {
            $message = "Failed to delete user";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }
}
