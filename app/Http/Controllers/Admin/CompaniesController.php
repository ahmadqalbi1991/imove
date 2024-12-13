<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Category;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Validator;
use Hash;
use DB;
class CompaniesController extends Controller
{
    //
    public function index(){
        $page_heading = "Companies";
        $mode="List";
        return view('admin.company.list',compact('mode', 'page_heading'));
    }


    public function create($id=''){
        $page_heading = 'Company';
        $mode = "Create";

        $first_name  = '';
        $last_name  = '';
        $account_type  = '';
        $company_name  = '';
        $company_email = '';
        $dial_code = '';
        $phone = '';
        $company_status = '';
        $address = '';
        $latitude = '';
        $longitude = '';
        $logo= '';
        $banner = '';
        $about_us = '';
        $company_license= '';
        $categories = [];
        $permissions= [];
        $is_approved = '';
        $admin_share = '';
        $company_share = '';
        if($id){

            $mode = "Edit";
            $id = decrypt($id);
            $company = User::find($id);
            $company_name = $company->name;
            $first_name = $company->company->first_name;
            $last_name = $company->company->last_name;
            $account_type = $company->company->account_type;
            $company_email = $company->email;
            $dial_code = $company->dial_code;
            $phone = $company->phone;
            $company_status = $company->status;
            $address = $company->address;
            $latitude = $company->latitude;
            $longitude = $company->longitude;
            $logo = $company->company->logo;
            $banner = $company->company->banner;
            $about_us = $company->company->about_us;
            $company_license = $company->company->company_license;
            $categories = $company->company->categories->pluck('id');
            $is_approved = $company->company->is_approved;
            $admin_share = $company->company->admin_share;
            $company_share = $company->company->company_share;
            if(count($categories) > 0){
                $categories = $categories->toArray();
            }
            else{
                $categories = [];
            }
        }
        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');
        $route_back = route('company.list');
        return view('admin.company.create',compact('mode', 'page_heading','company_license','id','company_name','first_name','last_name','account_type','company_status','logo','banner','about_us','operations','site_modules','address','latitude','longitude','company_email','dial_code','company_email','phone','categories','is_approved','admin_share','company_share','route_back'));

    }


    public function submit(REQUEST $request){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('company.list');
        $id = $request->id;

            if($request->account_type == '' || $request->account_type == null){
                // $message = "Company Already Addded";
                // $errors['company_name'] = 'Company Already Exists With Same Name';
                $message = 'Validation error';
                $errors['account_type'] = 'Account type is required';
            }else{
                if($id){

                    if($request->account_type == '0'){

                        $rules = [
                            'categories'   => 'required',
                            'account_type' => 'required',
                            'first_name' => 'required',
                            'last_name' => 'required',
                            'email' => 'required|unique:users,email,'.$id,
                            'dial_code' => 'required',
                            'phone' => 'required|unique:users,phone,'.$id,
                            'address' => 'required',
                            'latitude' => 'required',
                            'longitude' => 'required',
                            'about_us' => 'required',
                        ];
                    }else{
                        $rules = [
                            'categories'   => 'required',
                            'account_type' => 'required',
                            'company_name' => 'required',
                            'email' => 'required|unique:users,email,'.$id,
                            'dial_code' => 'required',
                            'phone' => 'required|unique:users,phone,'.$id,
                            'address' => 'required',
                            'latitude' => 'required',
                            'longitude' => 'required',
                            'about_us' => 'required',
                        ];
                    }

                    $validator = Validator::make($request->all(),$rules);

                    if ($validator->fails()) {
                        $status = "0";
                        $message = "Validation error occured";
                        $errors = $validator->messages();
                    }
                    else {

                        if($request->file("logo") != null || $request->file("company_license") != null || $request->file("banner") != null){

                            $user = User::find($id);
                            $user->name = $request->company_name ?? ($request->first_name." ".$request->last_name);
                            $user->email = $request->email;
                            if($request->password != null){
                                $user->password = Hash::make($request->password);
                            }
                            $user->dial_code = $request->dial_code;
                            $user->phone = $request->phone;
                            $user->phone_verified = 1;
                            $user->role_id = 4;
                            $user->status = $request->company_status;
                            $user->address = $request->address;
                            $user->latitude = $request->latitude;
                            $user->longitude = $request->longitude;
                            $user->save();

                            if(!empty($user)){

                                $company   = Company::where('user_id',$id)->first();
                                $company->name    = $request->company_name;
                                $company->first_name    = $request->first_name;
                                $company->last_name    = $request->last_name;
                                $company->account_type  = $request->account_type;
                                $company->status  = $request->company_status;
                                $company->about_us  = $request->about_us;
                                $company->admin_share  = $request->admin_share;
                                $company->company_share  = $request->company_share;
                                $response = image_upload($request,'comapny','logo');

                                if($response['status']){
                                    $company->logo= $response['link'];
                                }

                                $response = image_upload($request,'comapny','banner');

                                if($response['status']){
                                    $company->banner = $response['link'];
                                }

                                $response = image_upload($request,'comapny','company_license');

                                if($response['status']){
                                    $company->company_license= $response['link'];
                                }

                                $company->save();

                                $selectedCategories = $request->categories;
                                $allCategories = $company->categories->pluck('id');
                                if(count($allCategories) > 0){
                                    $allCategories = $allCategories->toArray();
                                }else{
                                    $allCategories = [];
                                }

                                // Filter out unselected categories
                                $unselectedCategories = array_diff($allCategories, $selectedCategories);

                                // Update selected categories
                                $company->categories()->sync($selectedCategories);

                                // Remove unselected categories
                                $company->categories()->detach($unselectedCategories);
                            }
                        }
                        else{

                            $user = User::find($id);
                            $user->name = $request->company_name ?? ($request->first_name." ".$request->last_name);
                            $user->email = $request->email;
                            if($request->password != null){
                                $user->password = Hash::make($request->password);
                            }
                            $user->dial_code = $request->dial_code;
                            $user->phone = $request->phone;
                            $user->phone_verified = 1;
                            $user->role_id = 4;
                            $user->status = $request->company_status;
                            $user->address = $request->address;
                            $user->latitude = $request->latitude;
                            $user->longitude = $request->longitude;
                            $user->save();

                            if(!empty($user)){
                                $company   = Company::where('user_id',$id)->first();
                                $company->name    = $request->company_name;
                                $company->first_name    = $request->first_name;
                                $company->last_name    = $request->last_name;
                                $company->account_type  = $request->account_type;
                                $company->about_us  = $request->about_us;
                                $company->admin_share  = $request->admin_share;
                                $company->company_share  = $request->company_share;
                                $company->status  = $request->company_status;
                                $company->save();

                                $selectedCategories = $request->categories;
                                $allCategories = $company->categories->pluck('id');
                                if(count($allCategories) > 0){
                                    $allCategories = $allCategories->toArray();
                                }else{
                                    $allCategories = [];
                                }

                                // Filter out unselected categories
                                $unselectedCategories = array_diff($allCategories, $selectedCategories);

                                // Update selected categories
                                $company->categories()->sync($selectedCategories);

                                // Remove unselected categories
                                $company->categories()->detach($unselectedCategories);
                            }


                        }

                        $status = "1";
                        $message = "Company Updated Successfully";
                    }
                }else{

                    if($request->account_type == '0'){

                        $rules = [
                            'categories'   => 'required',
                            'account_type' => 'required',
                            'first_name' => 'required',
                            'last_name' => 'required',
                            'email'     => 'required|unique:users',
                            'password'  => 'required',
                            'dial_code' => 'required',
                            'phone'     => 'required|unique:users',
                            'address' => 'required',
                            'latitude' => 'required',
                            'longitude' => 'required',
                            'about_us' => 'required',
                        ];

                    }else{

                        $rules = [
                            'categories'   => 'required',
                            'account_type' => 'required',
                            'company_name' => 'required',
                            'email'     => 'required|unique:users',
                            'password'  => 'required',
                            'dial_code' => 'required',
                            'phone'     => 'required|unique:users',
                            'logo' => 'required',
                            'banner' => 'required',
                            'company_license' => 'required',
                            'address' => 'required',
                            'latitude' => 'required',
                            'longitude' => 'required',
                            'about_us' => 'required',
                        ];
                    }

                    $validator = Validator::make($request->all(),$rules);

                    if ($validator->fails()) {
                        $status = "0";
                        $message = "Validation error occured";
                        $errors = $validator->messages();
                    }
                    else {

                        $user = new User();
                        $user->name = $request->company_name ?? ($request->first_name." ".$request->last_name);
                        $user->email = $request->email;
                        $user->password = Hash::make($request->password);
                        $user->dial_code = $request->dial_code;
                        $user->phone = $request->phone;
                        $user->phone_verified = 1;
                        $user->role_id = 4;
                        $user->email_verified_at = Carbon::now();
                        $user->status = $request->company_status;
                        $user->address = $request->address;
                        $user->latitude = $request->latitude;
                        $user->longitude = $request->longitude;
                        $user->save();

                        if(!empty($user)){
                            $company   = new Company();
                            $company->name    = $request->company_name;
                            $company->first_name    = $request->first_name;
                            $company->last_name    = $request->last_name;
                            $company->account_type  = $request->account_type;
                            $company->status  = $request->company_status;
                            $company->about_us  = $request->about_us;
                            $company->admin_share  = $request->admin_share;
                            $company->company_share  = $request->company_share;
                            $company->user_id  = $user->id;

                            if($request->file("logo") != null){
                                $response = image_upload($request,'comapny','logo');

                                if($response['status']){
                                    $company->logo= $response['link'];
                                }
                            }

                            if($request->file("banner") != null){
                                $response = image_upload($request,'comapny','banner');

                                if($response['status']){
                                    $company->banner= $response['link'];
                                }
                            }

                            if($request->file("company_license") != null){
                                $response = image_upload($request,'comapny','company_license');

                                if($response['status']){
                                    $company->company_license= $response['link'];
                                }
                            }

                            $company->save();

                            $selectedCategories = $request->categories;

                            $company->categories()->sync($selectedCategories);

                            $status = "1";
                            $message = "Company Created Successfully";
                        }
                        else{
                            $status = "0";
                            $message = "Company Could Not Created";
                        }
                    }

                }

        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


    public function getCompanyList(Request $request){

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = User::join('companies','companies.user_id','=','users.id')->select([
            DB::raw('users.name::text as company_name'),
            DB::raw('users.email::text as company_email'),
            DB::raw('companies.account_type::text as account_type'),
            DB::raw('companies.is_approved::text as is_approved'),
            DB::raw('users.dial_code::text as dial_code'),
            DB::raw('users.phone::text as phone'),
            DB::raw('users.status::text as status'),
            DB::raw('users.status::text as status_text'),
            DB::raw('users.created_at::text as created_at'),
            DB::raw('users.id::text as id')
        ])->whereNotIn('users.id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id',4)
        ->addSelect(['total_new_requests' => Booking::selectRaw('count(*) as total_new_requests')
            ->whereColumn('companies.user_id', 'bookings.company_id')
            ->where('bookings.admin_response','pending')])
        ->addSelect(['total_inprogress_requests' => Booking::selectRaw('count(*) as total_inprogress_requests')
            ->whereColumn('companies.user_id', 'bookings.company_id')
            ->where('bookings.admin_response','approved')->where('bookings.status','!=','delivered')])
        ->addSelect(['total_delivered_requests' => Booking::selectRaw('count(*) as total_delivered_requests')
            ->whereColumn('companies.user_id', 'bookings.company_id')
            ->where('bookings.admin_response','approved')->where('bookings.status','=','delivered')]);
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('created_at',function($data){
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('phone', function ($data) {
            return "+" . $data['dial_code'] . " " . $data['phone'];
        });

        $dt->edit('status_text', function ($data) {
            $statusTextHtml = '';
            if ($data["status"] == 'active') {
                $statusTextHtml = '<div class="ticket active">
                <i class="fas fa-check-circle text-success"></i>'. ucfirst($data["status"]).' </div>';
            } else {
                $statusTextHtml = '<div class="ticket disabled">
                <i class="fas fa-times-circle text-danger"></i> Disabled
                </div>';
            }
            return $statusTextHtml;

        });

        $dt->edit('account_type', function ($data) {
            if($data['account_type'] == 0){
                return "Individual";
            }else{
                return "Company";
            }
        });


        $dt->add('total_requests', function ($data) {
            $bookings = DB::table('bookings')->where('company_id',$data['id'])->where('status','delivered')->count('*');
            return $bookings;
        });

        $dt->add('ratings', function ($data) {
            $stars = '';
            $rating = DB::table('booking_reviews')->where('company_id',$data['id'])->avg('rate');

            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $rating) {
                    $stars .= '<i class="fa fa-star"></i>';
                } else {
                    $stars .= '<i class="bx bx-star" aria-hidden="true"></i>';
                }
            }
            return $stars;
        });

        // $dt->edit('status',function($data){

        //     if($data['is_approved'] == 'approved'){
        //         if(get_user_permission('company','u')){
        //             $checked = ($data["status"]=='active')?"checked":"";
        //                 $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
        //                     <input type="checkbox" data-role="active-switch"
        //                         data-href="'.route('company.status_change', ['id' => encrypt($data['id'])]).'"
        //                         '.$checked.' >
        //                     <span class="slider round"></span>
        //                 </label>';
        //         }else{
        //             $checked = ($data["status"]=='active')?"Active":"InActive";
        //             $class = ($data["status"]=='active')?"badge-success":"badge-danger";
        //             $html = '<span class="badge '.$class.'" '.$checked.' </span>';
        //         }
        //         return $html;
        //     }
        //     else{

        //         if(get_user_permission('company','u')){
        //             $checked = ($data["status"]=='active')?"":"";
        //                 $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
        //                     <input type="checkbox" disabled  data-role="active-switch"
        //                         '.$checked.' >
        //                     <span class="slider round unapproved"></span>
        //                 </label>';
        //         }else{
        //             $checked = ($data["status"]=='active')?"Active":"InActive";
        //             $class = ($data["status"]=='active')?"badge-success":"badge-danger";
        //             $html = '<span class="badge '.$class.'" '.$checked.' </span>';
        //         }
        //         return $html;

        //     }
        // });


        $dt->add('action', function($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';

                    if(get_user_permission('company','v')){
                        $html.='<a class="dropdown-item"
                            href="'.route('company.view',['id'=>encrypt($data['id'])]).'"><i
                                class="bx bx-show"></i> View</a>';
                    }

                    if(get_user_permission('company','u')){
                        $html.='<a class="dropdown-item"
                        href="'.route('company.edit',['id'=>encrypt($data['id'])]).'"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
                    }

                    // if(get_user_permission('company','d')){
                    //     $html.='<a class="dropdown-item" data-role="unlink"
                    //         data-message="Do you want to remove this category?"
                    //         href="'.route('company.delete',['id'=>encrypt($data['id'])]).'"><i
                    //             class="flaticon-delete-1"></i> Delete</a>';
                    // }

                    if(get_user_permission('company','v')){
                        $html.='<a class="dropdown-item"
                            href="'.route('company.reviews',['id'=>encrypt($data['id'])]).'"><i
                                class="bx bx-star"></i> Reviews</a>';
                    }


                    if(get_user_permission('company','v')){
                        $html.='<a class="dropdown-item"
                            href="'.route('company.bookings',['id'=>encrypt($data['id']), 'status' => 'progress']).'"><i
                                class="bx bxs-truck"></i> In Progress Requests ('.$data['total_inprogress_requests'].')</a>';
                    }

                    if(get_user_permission('company','v')){
                        $html.='<a class="dropdown-item"
                            href="'.route('company.bookings',['id'=>encrypt($data['id']), 'status' => 'delivered']).'"><i
                                class="bx bxs-truck"></i> Delivered Requests ('.$data['total_delivered_requests'].')</a>';
                    }



                    // if (get_user_permission('company', 'u')) {
                    //     $html .= '<a class="dropdown-item"
                    //             href="' . route('blacklists.add', ['id' => encrypt($data['id'])]) . '"><i class="fa-solid fa-user-lock"></i> BlackList</a>';
                    // }

            $html.='</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function view($id){
        $page_heading = 'Company Detail';
        $mode = "Create";
        $first_name  = '';
        $last_name  = '';
        $account_type  = '';
        $company_name  = '';
        $company_email = '';
        $dial_code = '';
        $phone = '';
        $company_status = '';
        $address = '';
        $latitude = '';
        $longitude = '';
        $logo= '';
        $banner = '';
        $about_us = '';
        $company_license= '';
        $categories = [];
        $permissions= [];
        $total_requests = 0;
        $is_approved = '';
        $admin_share = 0;
        $company_share = 0;

        if($id){
            $mode = "Edit";
            $id = decrypt($id);
            $company = User::find($id);
            $company_name = $company->name;
            $first_name = $company->company->first_name;
            $last_name = $company->company->last_name;
            $account_type = $company->company->account_type;
            $company_email = $company->email;
            $dial_code = $company->dial_code;
            $phone = $company->phone;
            $company_status = $company->status;
            $address = $company->address;
            $latitude = $company->latitude;
            $longitude = $company->longitude;
            $logo = $company->company->logo;
            $banner = $company->company->banner;
            $about_us = $company->company->about_us;
            $company_license = $company->company->company_license;
            $categories = $company->company->categories->pluck('id');
            $rating = DB::table('booking_reviews')->where('company_id',$id)->avg('rate');
            $total_requests = DB::table('bookings')->where('company_id',$id)->where('status','delivered')->count('*');
            $is_approved = $company->company->is_approved;
            $admin_share = $company->company->admin_share;
            $company_share = $company->company->company_share;

            if(count($categories) > 0){
                $categories = $categories->toArray();
            }
            else{
                $categories = [];
            }
        }

        $total_inprogress_requests = Booking::select('id')
            ->where('bookings.company_id',$id)
            ->where('bookings.admin_response','approved')->where('bookings.status','!=','delivered')
            ->count();

        $total_deliverd_requests = Booking::select('id')
            ->where('bookings.company_id',$id)
            ->where('bookings.admin_response','approved')->where('bookings.status','=','delivered')
            ->count();

        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');
        $route_back = route('company.list');
        return view('admin.company.view',compact('mode', 'page_heading','company_license','id','company_name','first_name','last_name','account_type','company_status','logo','banner','about_us','operations','site_modules','address','latitude','longitude','company_email','dial_code','company_email','phone','categories','rating','total_requests','is_approved','admin_share','company_share','route_back','total_inprogress_requests','total_deliverd_requests'));

    }


    public function change_status(REQUEST $request,$id){
        $status = "0";
        $message = "";
        $o_data  = [];
        $errors = [];

        $id = decrypt($id);

        $item = User::where(['id'=>$id])->get();

        if($item->count() > 0){
            User::where('id',$id)->update(['status'=>$request->status == '1'?'active':'inactive']);
            Company::where('user_id','=',$id)->update(['status'=>$request->status == '1'?'active':'inactive']);
            $status = "1";
            $message= "Status changed successfully";
        }else{
            $message = "Failed to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function delete(REQUEST $request,$id) {
        $status = "0";
        $message = "";


        $id = decrypt( $id );

        $company = User::where(['id'=>$id])->get();

        if( $company ) {
            User::where(['id' => $id])->delete();
            Company::where(['user_id' => $id])->delete();
            $message = "Company deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid Company data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }

    public function company_approve($id){

        $id = decrypt($id);
        $company = Company::where('user_id',$id)->first();

        if(!empty($company)){
            $company->is_approved = 'approved';
            $company->save();
            User::where('id',$id)->update(['status' => 'active']);
            session()->flash('success','Company Request Approved Successfully');
        }
        else{
            session()->flash('error','Company Request Could Not Approve');
        }
        return redirect()->back();

    }

    public function company_reject($id){

        $id = decrypt($id);
        $company = Company::where('user_id',$id)->first();

        if(!empty($company)){
            $company->is_approved = 'rejected';
            $company->save();
            User::where('id',$id)->update(['status' => 'inactive']);
            session()->flash('success','Company Request Rejected Successfully');
        }
        else{
            session()->flash('error','Company Request Could Not Reject');
        }
        return redirect()->back();

    }

    public function booking_reject($id){

        $id = decrypt($id);

        $booking = Booking::find($id);

        if(!empty($booking)){
            $booking->admin_response = 'rejected';
            $booking->save();
            if($booking){
                session()->flash('success','Customer Request Rejected Successfully');
            }
            else{
                session()->flash('error','Customer Request Could Not Reject');
            }
        }
        else{
            session()->flash('error','Customer Request Could Not Reject');
        }

        return redirect()->back();
    }

    public function company_bookings($id,$status)
    {
        $page_heading = 'Companies';
        if($status == 'progress'){
            $mode = "In Progress Requests";
        }else{
            $mode = "Delivered Requests";
        }

        //$mode = "List";
        $route_back = route('company.list');
        return view('admin.company.booking_list', compact('mode', 'page_heading','status','id','route_back'));
    }

    public function getCompanyBookingList(Request $request, $id, $status){

        $id = decrypt($id);
        if($status == 'progress'){
            $sqlBuilder = Booking::join('users as customers','customers.id','=','bookings.sender_id')->join('categories','categories.id','=','bookings.category_id')->leftJoin('users as companies','companies.id','=','bookings.company_id')->select([
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
            ])->where('admin_response','approved')->where('bookings.status','!=','delivered')->where('bookings.company_id',$id)->orderBy('bookings.id','DESC');//
        }else{
            $sqlBuilder = Booking::join('users as customers','customers.id','=','bookings.sender_id')->join('categories','categories.id','=','bookings.category_id')->leftJoin('users as companies','companies.id','=','bookings.company_id')->select([
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
            ])->where('admin_response','approved')->where('bookings.status',$status)->where('bookings.company_id',$id)->orderBy('bookings.id','DESC');//
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
            $html .= '('.(number_format($data['qouted_amount'],3) ?? number_format(0)).')';
            return $html;
        });

        $dt->edit('is_paid', function ($data) {
            $status = '';
            $status_color = '';
            if($data['is_paid'] == 'no'){
                $status = 'UNPAID';
                $status_color = 'danger';
            }
            else if($data['is_paid'] == 'yes'){
                $status = 'PAID';
                $status_color = 'info';
            }

            $statuses = ['unpaid','paid'];

            $html = '';

            $html = '<span class="badge badge-'.$status_color.'">'.$status.'</span>';

            return $html;
        });

        $dt->edit('booking_status', function ($data) {
            $status = '';
            $status_color = '';
            if($data['booking_status'] == 'customer_requested'){
                $status = 'Customer Requested';
                $status_color = 'secondary';
            }
            else if($data['booking_status'] == 'company_qouted'){
                $status = 'Company Qouted';
                $status_color = 'warning';
            }
            else if($data['booking_status'] == 'customer_accepted'){
                $status = 'Customer Qoute Accepted';
                $status_color = 'success';
            }
            else if($data['booking_status'] == 'journey_started'){
                $status = 'JOURNEY STARTED';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'item_collected'){
                $status = 'ITEM COLLECTED';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'on_the_way'){
                $status = 'On THE WAY';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'delivered'){
                $status = 'DELIVERED';
                $status_color = 'primary';
            }
            $statuses = ['customer_requested','company_qouted','customer_accepted','item_collected','on_the_way','delivered'];

            $html = '';
            if (get_user_permission('bookings', 'u')) {

                $html = '<span class="badge badge-'.$status_color.'">'.$status.'</span>';

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
            }else{
                $html = '<span class="badge badge-'.$status_color.'">'.$status.'</span>';
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
                   href="' . route('booking.qoutes', ['id' => encrypt($data['id']), 'type'=>'Companies']) . '"><i
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

}
