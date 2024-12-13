<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomerType;
use App\Models\Booking;
use App\Models\CompanyCategory;
use App\Models\Wallet;
use App\Models\Country;
use Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\BookingPickUpOrder;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use App\Models\Role;
use App\Models\RolePermissions;
use Response;



class CustomerController extends Controller
{
    public function index(REQUEST $request)
    {
        $page_heading = "Customers";
        $mode = "List";
        return view('admin.customers.list', compact('mode', 'page_heading'));
    }

    public function getcustomerList(Request $request)
    {

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = User::select([
            'email',
            'dial_code',
            'phone',
            'user_image',
            DB::raw('users.name::text as user_name'),
            DB::raw('user_status::text as user_status'),
            DB::raw('users.created_at::text as created_at'),
            DB::raw('users.id::text as id')
        ])->where(['role_id' => 2])->orderBy('id','desc'); //
        return DataTables::of($sqlBuilder)
        ->editColumn('created_at', function ($data) {

            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        })
        ->editColumn('phone', function ($data) {
            return "+" . $data['dial_code'] . " " . $data['phone'];
        })
        // $dt->edit('user_image', function ($data) {
        //     return "
        //     <ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>
        //         <li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-xs pull-up' aria-label='Sophia Wilkerson'  data-bs-original-title='Sophia Wilkerson'>
        //             <img class='rounded-circle' src='" . get_uploaded_image_url($data['user_image'], 'user_image_upload_dir') . "' style='width:50px; height:50px;'>
        //         </li>
        //     </ul>";
        // });
        
        ->editColumn('user_status', function ($data) {
            if (get_user_permission('users', 'u')) {
                $checked = ($data["user_status"] == 1) ? "checked" : "";
                $html = '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="' . route('users.status_change', ['id' => encrypt($data['id'])]) . '"
                            ' . $checked . ' >
                        <span class="slider round"></span>
                    </label>';
            } else {
                $checked = ($data["user_status"] == 1) ? "Active" : "InActive";
                $class = ($data["user_status"] == 1) ? "badge-success" : "badge-danger";
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
            // if (get_user_permission('users', 'v')) {
            //     $html .= '<a class="dropdown-item"
            //             href="' . route('customers.view', ['id' => encrypt($data['id'])]) . '"><i
            //                 class="bx bx-file"></i> View</a>';
            // }
            if (get_user_permission('users', 'u')) {
                $html .= '<a class="dropdown-item"
                       href="' . route('customers.edit', ['id' => encrypt($data['id'])]) . '"><i
                           class="flaticon-pencil-1"></i> Edit</a>';
            }
            if (get_user_permission('users', 'u')) {
                $html .= '<a class="dropdown-item"
                       href="' .route('vehicles.list',$data['id']) . '"><i
                           class="flaticon-pencil-1"></i> Vehicles</a>';
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
        })

        ->rawColumns(['user_status', 'action'])
        ->make(true);
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
            User::where('id', '=', $id)->update(['user_status' => $request->status]);
            $status = "1";
            $message = "Status changed successfully";
        } else {
            $message = "Failed to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }
    function view($id)
    {
        $id = decrypt($id);
        $page_heading = "Customer";
        $mode = "Information";
        $user = User::findOrFail($id);
        return view('admin.customers.view', compact('mode', 'page_heading', 'user'));
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $page_heading = "Customer";
        $mode = "Edit";
        $user = User::find($id);
        $customer_types = CustomerType::where('deleted_at', null)->where('status', 'active')->get();
        if (!empty($user)) {
            return view('admin.customers.edit', compact('mode', 'page_heading', 'user', 'customer_types'));
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id)
    {

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('customers.list');
        $rules = [
            'customer_type_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'dial_code' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }

        $user = User::find($id);

        if (!empty($user)) {
            $user->name = $request->name;
            $name = $request->name;
            $nameParts = explode(' ', $name, 2); // Split into two parts, first and last name

            $first_name = $nameParts[0]; // The first part will be the first name
            $last_name = isset($nameParts[1]) ? $nameParts[1] : '';
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->user_status = $request->user_status;
            $user->address = $request->address;
            $user->customer_type_id = $request->customer_type_id;
            $user->dial_code = $request->dial_code;

            $user->save();
            $status = "1";
            $message = "Customer Updated Successfully";
        } else {
            $message = "Failed to change customer Information";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function listCust(REQUEST $request)
    {
        $page_heading = "Customers";
        $mode = "List";
        return view('admin.customerData.list', compact('mode', 'page_heading'));
    }

    

    public function getcustomerTotalList(Request $request)
{
    // Build the SQL query
    $sqlBuilder = User::select([
        DB::raw('users.name::text as name'), 
        DB::raw('users.dial_code::text as dial_code'),
        DB::raw('users.phone::text as phone'),
        
        DB::raw('status::text as status'),
        DB::raw('status::text as status_text'),
        DB::raw('users.created_at::text as created_at'),
        'users.id AS id'
    ])
    ->whereNotIn('users.id', function ($query) {
        $query->select('user_id')
              ->from('blacklists')
              ->whereColumn('users.id', '=', 'blacklists.user_id');
    })
    ->where('role_id', 3)
    ->where('deleted', '!=', 1);

    

    // Add additional counts for new, rejected, in-progress, and delivered requests
    

    return DataTables::of($sqlBuilder)
        ->editColumn('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        })
         ->editColumn('phone', function ($data) {
            return '+'.$data['dial_code'].$data['phone'];
        })
        ->editColumn('status', function ($data) {
            if (get_user_permission('customers', 'u')) {
                $checked = ($data["status"] == 'active') ? "checked" : "";
                $html = '<label class="switch s-icons s-outline s-outline-warning mb-4 mr-2">
                        <input type="checkbox" data-role="active-switch"
                            data-href="' . route('customers.status_active', ['id' => encrypt($data['id'])]) . '"
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

            if (get_user_permission('customers', 'v')) {
                $html .= '<a class="dropdown-item"
                            href="' . route('customer.view.data', ['id' => encrypt($data['id'])]) . '"><i
                                class="bx bx-show"></i> View</a>';
            }
            if (get_user_permission('users', 'u')) {
                $html .= '<a class="dropdown-item"
                       href="' .route('vehicles.list',$data['id']) . '"><i
                           class="flaticon-pencil-1"></i> Vehicles</a>';
            }
            if (get_user_permission('customers', 'u')) {
                $html .= '<a class="dropdown-item"
                        href="' . route('customer.edit.data', ['id' => encrypt($data['id'])]) . '"><i
                            class="bx bx-edit"></i> edit</a>';
            }

            if (get_user_permission('customers', 'v')) {
                $html .= '<a class="dropdown-item"
                            href="' . route('bookings.list.new', ['cus_id' => $data['id']]) . '"><i
                                class="bx bxs-truck"></i> Bookings</a>';
            }
            if(get_user_permission('customers','d')){
                $html.='<a class="dropdown-item" data-role="unlink"
                    data-message="Do you want to remove this Customer?"
                    href="'.route('customers.delete',['id'=>encrypt($data['id'])]).'"><i
                        class="flaticon-delete-1"></i> Delete</a>';
                }

            $html .= '</div></div>';
            return $html;
        })
        ->rawColumns(['status', 'action'])
        ->make(true);
}



    public function detailView($id = '')
    {

        $page_heading = 'Customer';
        $mode = "Create";
        $user  = '';
        $status = '';
        $name = '';
        $phone = '';
        $email = '';
        $dial_code = '';
        $password = '';
        $permissions = [];
        $first_name = '';
        $last_name = '';

        if ($id) {

            $mode = "Detail";
            $id = decrypt($id);
            $user = User::find($id);
            $name = $user->name;
            $phone = $user->phone;
            $status = $user->status;
            $email = $user->email;
            $dial_code = $user->dial_code;
            $password = $user->password;
            $first_name = $user->first_name;
            $last_name = $user->last_name;
        }
        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');
        $route_back = route('customers.list.all');
        $countries = Country::get();
            
        return view('admin.customerData.insert', compact('first_name','last_name','mode', 'status', 'countries','page_heading', 'id', 'user', 'name', 'email', 'phone', 'password', 'dial_code', 'route_back'));
    }



    public function change_status_cus(REQUEST $request, $id)
    {
        $status = "0";
        $message = "";
        $o_data  = [];
        $errors = [];

        $id = decrypt($id);

        $item = User::where(['id' => $id])->get();

        if ($item->count() > 0) {

            User::where('id', '=', $id)->update(['status' => $request->status == '1' ? 'active' : 'inactive']);
            $status = "1";
            $message = "Status changed successfully";
        } else {
            $message = "Failed to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function createCus()
    {
        $page_heading = 'Create Customer';
        $mode = "Import CSV";
        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');
        return view('admin.customerData.create', compact('mode', 'page_heading'));
    }




    public function submitCsv(REQUEST $request)
    {

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('customers.list.all');
        $rules = [
            'customer_csv' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {

            $file = $request->file('customer_csv');

            // Save the file to the storage directory
            $path = $file->store('csv_files');
            $csvData = file_get_contents(storage_path('app/' . $path));
            // Parse the CSV data
            $rows = str_getcsv($csvData, "\n");
            $data = array_map('str_getcsv', file($file));

            foreach ($data as $key => $row) {
                if ($key === 0) {
                    continue;
                }
                if (User::where('email', '=', $row[1])->exists()) {
                    continue;
                }


                $customer   = new User();
                $customer->name    = $row[0];
                $customer->email  = $row[1];
                $customer->dial_code    = $row[2];
                $customer->phone    = $row[3];
                $customer->status  = $row[4];
                $customer->password  = Hash::make($row[5]);
                $customer->role_id  = 3;
                $customer->email_verified_at  = Carbon::now();
                $customer->phone_verified  = 1;

                $customer->save();





                $wallet = new Wallet();
                $wallet->user_id = $customer->id;
                $wallet->amount = 0;
                $wallet->save();
            }



            // Insert the data into the database


            $status = "1";
            $message = "Customers Data Addded Successfully";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


    public function insert(REQUEST $request)
    {

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('customers.list.all');
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            
            'customer_email' => 'required',
            'dial_code' => 'required',
            'phone' => 'required',
            
            
        ];

        if ($request->id == '') {
            $rules['password'] = 'required';
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            
            $name  = $request->customer_name;
            $email = $request->customer_email;
            $dial_code  = $request->dial_code;
            $phone = $request->phone;
            $password  = $request->password;
            $status = $request->status;
            $id         = $request->id;
            $check      = User::whereRaw('Lower(email) = ?', [strtolower($email)])->where('id', '!=', $id)->get();

            if ($check->count() > 0) {
                $message = "Customer Already Addded";
                $errors['customer_name'] = 'Customer Already Addded';
            } else {
                if ($id) {
                    $customer   = User::find($id);
                    $customer->name    = $name;
                    $customer->email  = $email;
                    $customer->dial_code    = $dial_code;
                    $customer->phone    = $phone;
                    if ($request->password != null) {
                        $customer->password  = Hash::make($password);
                    }
                    
                    // Split into two parts, first and last name

                    
                   
                    
                    $customer->address_2 = $request->address_2;
                    $customer->country = $request->country;
                    $customer->city = $request->city;
                    $customer->zip_code = $request->zip_code;
                    
                    $customer->first_name = $request->first_name;
                    $customer->last_name = $request->last_name;
                    $customer->name = $request->first_name.' '.$request->last_name;

                    $customer->role_id  = 3;
                    $customer->status  = $status;

                    $customer->save();

                    $status = "1";
                    $message = "Customer Updated Successfully";
                } else {
                    $customer   = new User();
                    $customer->first_name = $request->first_name;
                    $customer->last_name = $request->last_name;
                    $customer->name = $request->first_name.' '.$request->last_name; // Split into two parts, first and last name

                    
                    $customer->email  = $email;
                    $customer->dial_code    = $dial_code;
                    $customer->phone    = $phone;
                    $customer->password  = Hash::make($password);
                    $customer->role_id  = 3;
                    $customer->status  = $status;

                   
                    $customer->address_2 = $request->address_2;
                    $customer->country = $request->country;
                    $customer->city = $request->city;
                    $customer->zip_code = $request->zip_code;
                   

                    $customer->email_verified_at  = Carbon::now();
                    $customer->phone_verified  = 1;

                    $customer->save();


                    $wallet = new Wallet();
                    $wallet->user_id = $customer->id;
                    $wallet->amount = 0;
                    $wallet->save();

                    $status = "1";
                    $message = "Customer Addded Successfully";
                }

                $trade_license ='';
                if ($request->file("trade_license")) {
                    $response = image_upload($request, 'users', 'trade_license');
                    if ($response['status']) {
                        $trade_license = $response['link'];
                    }
                }

                $customer->customer_type= $request->customer_type;
                $customer->company_name= $request->company_name;
                if($trade_license!=''){
                $customer->trade_license=$trade_license;
                }
                $customer->save();
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


    public function detailShow($id)
    {
        $page_heading = 'Customer';
        $mode = "Create";
        $user  = '';
        $status = '';
        $name = '';
        $phone = '';
        $email = '';
        $dial_code = '';
        $password = '';
        $permissions = [];

        if ($id) {
            $id = decrypt($id);
            $user = User::find($id);
            $mode = $user->name;
            
            
            $name = $user->name;
            $phone = $user->phone;
            $status = $user->status;
            $email = $user->email;
            $dial_code = $user->dial_code;
            $password = $user->password;
        }
        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');
        $route_back = route('customers.list.all');

        $total_new_requests = Booking::select('*')
            ->where('bookings.sender_id', $id)
            ->where('bookings.admin_response', 'pending')
            ->count();

        $total_rejected_requests = Booking::select('*')
            ->where('bookings.sender_id', $id)
            ->where('bookings.admin_response', 'rejected')
            ->count();

        $total_inprogress_requests = Booking::select('*')
            ->where('bookings.sender_id', $id)
            ->where('bookings.admin_response', 'approved')->where('bookings.status', '!=', 'delivered')
            ->count();

        $total_delivered_requests = Booking::select('*')
            ->where('bookings.sender_id', $id)
            ->where('bookings.admin_response', 'approved')->where('bookings.status', '=', 'delivered')
            ->count();

        return view('admin.customerData.detail', compact('mode', 'status', 'page_heading', 'id', 'user', 'name', 'email', 'phone', 'password', 'dial_code', 'route_back', 'total_new_requests', 'total_rejected_requests', 'total_inprogress_requests', 'total_delivered_requests'));
    }

    public function exportCsv()
    {
        $csvFile = public_path('csv/demo.csv');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="demo.csv"',
        ];

        return Response::download($csvFile, 'demo.csv', $headers);
    }

    public function customer_bookings($id, $status)
    {
        $page_heading = 'Customers';
        if ($status == 'progress') {
            $mode = "In Progress Requests";
        } else {
            $mode = "Delivered Requests";
        }

        //$mode = "List";
        $route_back = route('customers.list.all');
        return view('admin.customerData.booking_list', compact('mode', 'page_heading', 'status', 'id', 'route_back'));
    }

    public function getCustomerBookingList(Request $request, $id, $status)
    {

        $id = decrypt($id);

        if ($status == 'progress') {

            $sqlBuilder = Booking::join('users as customers', 'customers.id', '=', 'bookings.sender_id')->join('categories', 'categories.id', '=', 'bookings.category_id')->leftJoin('users as companies', 'companies.id', '=', 'bookings.company_id')->select([
                'bookings.id as id',
                'bookings.booking_number as booking_number',
                'customers.name as customer_name',
                'companies.name as company_name',
                'categories.id as category_id',
                'categories.name as category_name',
                'bookings.status as booking_status',
                'bookings.qouted_amount as qouted_amount',
                'bookings.comission_amount as comission_amount',
                'bookings.is_paid as is_paid',
                'bookings.created_at as created_at',
            ])->where('admin_response', 'approved')->where('bookings.status', '!=', 'delivered')->where('bookings.sender_id', $id)->orderBy('bookings.id', 'DESC'); //
        } else {

            $sqlBuilder = Booking::join('users as customers', 'customers.id', '=', 'bookings.sender_id')->join('categories', 'categories.id', '=', 'bookings.category_id')->leftJoin('users as companies', 'companies.id', '=', 'bookings.company_id')->select([
                'bookings.id as id',
                'bookings.booking_number as booking_number',
                'customers.name as customer_name',
                'companies.name as company_name',
                'categories.id as category_id',
                'categories.name as category_name',
                'bookings.status as booking_status',
                'bookings.qouted_amount as qouted_amount',
                'bookings.comission_amount as comission_amount',
                'bookings.is_paid as is_paid',
                'bookings.created_at as created_at',
            ])->where('admin_response', 'approved')->where('bookings.status', 'delivered')->where('bookings.sender_id', $id)->orderBy('bookings.id', 'DESC'); //
        }

        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('booking_number', function ($data) {
            $html = '';
            $html .= $data['booking_number'];
            return $html;
        });

        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('qouted_amount', function ($data) {
            $html = '';
            $html .= $data['company_name'] ?? 'Not Approved Yet';
            $html .= '<br />';
            $html .= '(' . (number_format($data['qouted_amount'], 3) ?? number_format(0)) . ')';
            return $html;
        });

        $dt->edit('is_paid', function ($data) {
            $status = '';
            $status_color = '';
            if ($data['is_paid'] == 'no') {
                $status = 'UNPAID';
                $status_color = 'danger';
            } else if ($data['is_paid'] == 'yes') {
                $status = 'PAID';
                $status_color = 'info';
            }

            $statuses = ['unpaid', 'paid'];

            $html = '';

            $html = '<span class="badge badge-' . $status_color . '">' . $status . '</span>';

            return $html;
        });

        $dt->edit('booking_status', function ($data) {
            $status = '';
            $status_color = '';
            if ($data['booking_status'] == 'customer_requested') {
                $status = 'Customer Requested';
                $status_color = 'secondary';
            } else if ($data['booking_status'] == 'company_qouted') {
                $status = 'Company Qouted';
                $status_color = 'warning';
            } else if ($data['booking_status'] == 'customer_accepted') {
                $status = 'Customer Qoute Accepted';
                $status_color = 'success';
            } else if ($data['booking_status'] == 'journey_started') {
                $status = 'JOURNEY STARTED';
                $status_color = 'info';
            } else if ($data['booking_status'] == 'item_collected') {
                $status = 'ITEM COLLECTED';
                $status_color = 'info';
            } else if ($data['booking_status'] == 'on_the_way') {
                $status = 'On THE WAY';
                $status_color = 'info';
            } else if ($data['booking_status'] == 'delivered') {
                $status = 'DELIVERED';
                $status_color = 'primary';
            }
            $statuses = ['customer_requested', 'company_qouted', 'customer_accepted', 'item_collected', 'on_the_way', 'delivered'];

            $html = '';
            if (get_user_permission('bookings', 'u')) {

                $html = '<span class="badge badge-' . $status_color . '">' . $status . '</span>';

                // $html .= '<div class="dropdown" >';
                // $html .=            '<button class="btn btn-'.$status_color.' dropdown-toggle" type="button" data-toggle="dropdown">
                //                 '. $status.'
                //             <span class="caret"></span></button>';

                // $html .=   '<ul class="dropdown-menu">';
                // foreach($statuses as $st){
                //     if(strtoupper(str_replace('_',' ',$st)) == $status){
                //         continue;
                //     }

                //     $route = route('booking_status',['id' => $data['id'],'status' => $st]);
                //     $html .= '<li><a class="dropdown-item" href="'.$route.'">'.strtoupper(str_replace('_',' ',$st)) .'</a></li>';
                // }

                // $html .=    '</ul>';
                // $html .=    '</div>';
            } else {
                $html = '<span class="badge badge-' . $status_color . '">' . $status . '</span>';
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
            if (get_user_permission('bookings', 'v')) {
                $html .= '<a class="dropdown-item"
                        href="' . route('bookings.view', ['id' => encrypt($data['id'])]) . '"><i
                            class="bx bx-show"></i> View</a>';
            }
            if (get_user_permission('bookings', 'u')) {
                //    $html .= '<a class="dropdown-item"
                //            href="' . route('bookings.edit', ['id' => encrypt($data['id'])]) . '"><i
                //                class="flaticon-pencil-1"></i> Edit</a>';

                $html .= '<a class="dropdown-item"
                   href="' . route('booking.qoutes', ['id' => encrypt($data['id']), 'type'=>'Customers']) . '"><i
               class="bx bxs-truck"></i> Company Quotes</a>';

                //    $html .= '<a class="dropdown-item add-charges" href = "javascript::void(0)" data-id = "'.$data['id'].'"  
                //        ><i class="fa-solid fa-sack-dollar"></i></i> Add Charges</a>';
            }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function pending_bookings($id)
    {
        $status = 'pending';
        $page_heading =  "Customers";
        $booking_page_heading = "New Requests";
        $mode = "New Requests";
        $route_back = route('customers.list.all');
        return view('admin.customerData.booking_new', compact('mode', 'booking_page_heading', 'page_heading', 'id', 'status', 'route_back'));
    }

    public function rejected_bookings($id)
    {
        $status = 'rejected';
        $page_heading =  "Customers";
        $booking_page_heading = "Rejected Requests";
        $mode = "Rejected Requests";
        $route_back = route('customers.list.all');
        return view('admin.customerData.booking_new', compact('mode', 'booking_page_heading', 'page_heading', 'id', 'status', 'route_back'));
    }

    public function getCustomernewbookingList($id, $status)
    {

        $id = decrypt($id);
        $sqlBuilder = Booking::join('users as customers', 'customers.id', '=', 'bookings.sender_id')->join('categories', 'categories.id', '=', 'bookings.category_id')->select([
            'bookings.id as id',
            'bookings.booking_number as booking_number',
            'categories.id as category_id',
            'categories.name as category_name',
            'customers.name as customer_name',
            'bookings.created_at as created_at',
            'bookings.status as booking_status',
            'bookings.admin_response as admin_response',
        ])->addSelect(['total_companies' => CompanyCategory::selectRaw('count(*) as total_categories')
            ->whereColumn('categories.id', 'company_categories.category_id')])->where('admin_response', $status)->where('bookings.sender_id', $id)->orderBy('bookings.id', 'DESC'); //
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);


        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('category_name', function ($data) {
            $html = '';
            $html .= '<b>' . $data['category_name'] . '</b>';
            $html .= '<br><small> Total Companies (' . $data['total_companies'] . ') </small>';
            return $html;
        });


        $dt->add('action', function ($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
            if (get_user_permission('bookings', 'v')) {
                $html .= '<a class="dropdown-item"
                        href="' . route('bookings.view', ['id' => encrypt($data['id'])]) . '"><i
                            class="bx bx-show"></i> View</a>';
            }

            if ($data['admin_response'] == 'pending' && $data['total_companies'] > 0) {

                if (get_user_permission('bookings', 'u')) {
                    $html .= '<a class="dropdown-item"
                        href="' . route('booking.approve', ['id' => encrypt($data['id'])]) . '"><i
                    class="fa fa-check"></i> Approve</a>';
                }

                if (get_user_permission('bookings', 'u')) {
                    $html .= '<a class="dropdown-item"
                        href="' . route('booking.reject', ['id' => encrypt($data['id'])]) . '"><i
                    class="fa fa-times"></i> Reject</a>';
                }
            }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
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
            $user=$item;

            $datamain = BookingPickUpOrder::where('customer_id',$user->id)->where('booking_status','!=',8)->get();
            if($datamain->first()){
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
