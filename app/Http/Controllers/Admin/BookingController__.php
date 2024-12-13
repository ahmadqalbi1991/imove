<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Care;
use App\Models\Size;
use App\Models\TruckType;
use App\Models\Wallet;
use App\Models\ShippingMethod;
use App\Models\DriverDetail;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Costing;
use App\Models\Deligate;
use App\Models\user_wallet_transactions;
use App\Models\CompanyCategory;
use App\Models\BookingQoute;
use App\Models\BookingAdditionalCharge;
use App\Models\BookingStatusTracking;
use App\Models\BookingPickUpOrder;
use App\Models\BookingDropOffOrder;
use App\Models\RequestImages;
use App\Models\Settings;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;
use App\Mail\CustomerRequestMail;
use App\Mail\DriverRequestMail;
use App\Mail\DriverQoutedRequest;
use App\Mail\CustomerRequestUpdateMail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Kreait\Firebase\Contract\Database;
use Mail;

class BookingController extends Controller
{
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    public function index(REQUEST $request)
    {
        $page_heading = "In Progress Requests";
        $mode = "List";
        return view('admin.bookings.list', compact('mode', 'page_heading'));
    }

    public function index_total(REQUEST $request)
    {
        $page_heading = "All Requests";
        $mode = "List";
        return view('admin.bookings.list_total', compact('mode', 'page_heading'));
    }

    public function gettotalbookingList(){

        $sqlBuilder = Booking::join('users as customers','customers.id','=','bookings.sender_id')
        ->join('categories','categories.id','=','bookings.category_id')
        ->leftJoin('users as companies','companies.id','=','bookings.company_id')
        ->select(
            [
            DB::raw('bookings.id::text as id'),
            DB::raw('bookings.booking_number::text as booking_number'),
            DB::raw('customers.name::text as customer_name'),
            DB::raw('companies.name::text as company_name'),
            DB::raw('categories.id::text as category_id'),
            DB::raw('categories.name::text as category_name'),
            DB::raw('bookings.status::text as booking_status'),
            DB::raw('bookings.created_at::text as created_at'),
            DB::raw('bookings.admin_response::text as admin_response'),
            DB::raw('bookings.qouted_amount::text as qouted_amount'),
            DB::raw('bookings.is_paid::text as is_paid'),
            DB::raw('bookings.created_at::text as created_at'),
        ])->addSelect(['total_companies' => CompanyCategory::selectRaw('count(*) as total_categories')
        ->whereColumn('categories.id', 'company_categories.category_id')])->orderBy('bookings.id','DESC');//
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('booking_number', function ($data) {
            $html = '';
            $html .= $data['booking_number'];
            return $html;
        });

        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y h:i A');
        });

        $dt->edit('category_name', function ($data) {
            $html = '';
            $html .= '<b>'.$data['category_name'].'</b>';
            $html .= '<br><small> Total Companies ('.$data['total_companies'].') </small>';
            return $html;
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
                $status = 'Company Quoted';
                $status_color = 'warning';
            }
            else if($data['booking_status'] == 'customer_accepted'){
                $status = 'Customer Quote Accepted';
                $status_color = 'success';
            }
            else if($data['booking_status'] == 'journey_started'){
                $status = 'Journey Started';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'item_collected'){
                $status = 'Item Collected';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'on_the_way'){
                $status = 'On The Way';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'delivered'){
                $status = 'Delivered';
                $status_color = 'primary';
            }
            $statuses = ['customer_requested','company_qouted','customer_accepted','item_collected','on_the_way','delivered'];

            $html = '';
            if (get_user_permission('bookings', 'u')) {

                $html = '<span class="badge badge-'.$status_color.'">'.ucwords($status).'</span>';
            }else{
                $html = '<span class="badge badge-'.$status_color.'">'.ucwords($status).'</span>';
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


            if($data['admin_response'] == 'pending' && $data['total_companies'] > 0){

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

            if($data['admin_response'] == 'approved'){
               $html .= '<a class="dropdown-item"
                   href="' . route('booking.qoutes', ['id' => encrypt($data['id']), 'type'=>'Companies']) . '"><i
               class="bx bxs-truck"></i> Company Quotes</a>';
            }



            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();

    }

    public function create(){

        $page_heading = "Create Booking";
        $mode = "create";
        $customers = User::where('status','active')->where('role_id',3)->get();
        $trucks = TruckType::where('status','active')->get();
        $shipping_methods = ShippingMethod::where('status','active')->get();
        return view('admin.bookings.create',compact('customers','page_heading','mode','trucks','shipping_methods'));

    }

    public function store(Request $request){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('bookings.list');
        $rules = [
            'customer' => 'required',
            'truck_type' => 'required',
            'drivers' => 'required',
            'shipping_method' => 'required',
            'quantity' => 'required',
            'dial_code' => 'required',
            'collection_address' => 'required',
            'deliver_address' => 'required',
            'receiver_name' => 'required',
            'receiver_email' => 'required',
            'receiver_phone' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();

        }
        else{

            $deligate = Deligate::where('slug','truck')->first();

            $booking = Booking::create([
                'collection_address' => $request->collection_address,
                'deliver_address' => $request->deliver_address,
                'sender_id' => $request->customer,
                'receiver_name' => $request->receiver_name,
                'receiver_email'=> $request->receiver_email,
                'receiver_phone' => ($request->dial_code." ".$request->receiver_phone),
                'deligate_id' => $deligate->id,
                'deligate_details' => 'truck',
                'truck_type_id' => $request->truck_type,
                'shipping_method_id' => $request->shipping_method,
                'invoice_number' => $request->invoice_number,
                'quantity' => $request->quantity,
                'admin_response' => 'ask_for_qoute',
                'status' => 'pending',
            ]);

            if(!empty($booking)){
                $booking_number = sprintf("%06d", $booking->id);
                $booking->booking_number = "#TX-".$booking_number;
                $booking->save();

                if($booking->status == 'pending'){
                    $status_booking = 'request_created';
                }
                else{
                    $status_booking = $booking->status;
                }

                BookingStatusTracking::updateOrCreate(['booking_id' => $booking->id,'status_tracking' => $status_booking],['status_tracking' => $status_booking]);
            }

            if(!empty($booking) && count($request->drivers) > 0){

                foreach($request->drivers as $driver){

                    $booking_qoute = new BookingQoute();
                    $booking_qoute->booking_id = $booking->id;
                    $booking_qoute->driver_id = $driver;
                    $booking_qoute->price = 0.00;
                    $booking_qoute->hours = 0;
                    $booking_qoute->status = 'pending';
                    $booking_qoute->save();
                    $data['driver'] = User::find($booking_qoute->driver_id);
                    $data['booking'] = $booking;
                    if(env('MAILS_ENABLE')){
                        Mail::to($data['driver']->email)->send(new DriverRequestMail($data));
                    }
                }

            }

            if(!empty($booking) && !empty($booking_qoute)){
                $data['user'] = User::find($booking->sender_id);
                $data['booking'] = $booking;
               if(env('MAILS_ENABLE')){
                    Mail::to($data['user']->email)->send(new CustomerRequestMail($data));
                }
            }

            if(!empty($booking) && !empty($booking_qoute)){

                $status = "1";
                $message = "Booking has been created successfully";

            }
            else
            {
                $status = "0";
                $message = "Booking could not be created";
            }

        }

         echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


    public function edit($id){
        $id = decrypt($id);
        $booking = Booking::find($id);
        if(empty($booking)){
            abort(404);
        }
        $deligate = Deligate::where('id',$booking->deligate_id)->first();
        $page_heading = "Edit Booking";
        $mode = "edit";
        $shipping_methods = ShippingMethod::where('status','active')->get();
        $drivers = User::where('status','active')->where('role_id',2)->get();
        $customers = User::where('status','active')->where('role_id',3)->get();
        $trucks = TruckType::where('status','active')->get();
        $selected_drivers = $booking->booking_qoutes->pluck('driver_id')->toArray();

        return view('admin.bookings.edit',compact('customers','page_heading','mode','trucks','booking','drivers','selected_drivers','deligate','shipping_methods'));

    }


    public function view($id){
        $id = decrypt($id);
        $booking = Booking::find($id);
        $view = '';
        $compact = [];
        $compact['page_heading'] = "List";
        //$compact['page_heading'] = "Request Details For ".$booking->category->name;
        $compact['image_heading'] = '<img src = "'.$booking->category->icon.'" width = "100" class = "category-img">';
        $compact['mode'] = "view";


        if(url()->previous() != url()->current()){
            $compact['route_back'] = url()->previous();
        }else{
            $compact['route_back'] = route('bookings.list.new');
        }

        if(empty($booking)){
            abort(404);
        }else{

            if($booking->category_id == Category::DomesticHomeRelocation){
                $view = 'admin.bookings.domestic_home_relocation';
                $compact['booking'] = $booking;
            }
            else if($booking->category_id == Category::InternationalHomeRelocation){
                $view = 'admin.bookings.international_home_relocation';
                $compact['booking'] = $booking;
            }
            else if($booking->category_id == Category::OfficeRelocation){
                $view = 'admin.bookings.office_relocation';
                $compact['booking'] = $booking;
            }
            else if($booking->category_id == Category::StorageServices){
                $view = 'admin.bookings.storage_services';
                $compact['booking'] = $booking;
            }
            else{
                abort(404);
            }



        }

        return view($view,$compact);

    }


     public function update(Request $request,$id){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('bookings.list');
        $rules = [
            'customer' => 'required',
//            'truck_type' => 'required',
//            'drivers' => 'required',
            'shipping_method' => 'required',
            'quantity' => 'required',
            'dial_code' => 'required',
            'collection_address' => 'required',
            'deliver_address' => 'required',
            'receiver_name' => 'required',
            'receiver_email' => 'required',
            'receiver_phone' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();

        }
        else{
            $booking_number = sprintf("%06d", $id);
            $booking = Booking::where('id',$id)->update([
                'collection_address' => $request->collection_address,
                'deliver_address' => $request->deliver_address,
                'sender_id' => $request->customer,
                'receiver_name' => $request->receiver_name,
                'receiver_email'=> $request->receiver_email,
                'receiver_phone' => ($request->dial_code." ".$request->receiver_phone),
                'shipping_method_id' => $request->shipping_method,
                'invoice_number' => $request->invoice_number,
                'truck_type_id' => $request->truck_type,
                'quantity' => $request->quantity,
                'delivery_note' => $request->delivery_note,
                'booking_number' => "#TX-".$booking_number
            ]);


            if(!empty($booking) && ($request->drivers != null && count($request->drivers) > 0)){

                Booking::where('id',$id)->update([
                    'admin_response' => 'ask_for_qoute',
                ]);
                foreach($request->drivers as $driver){

                    $booking_qoute = new BookingQoute();
                    $booking_qoute->booking_id = $id;
                    $booking_qoute->driver_id = $driver;
                    $booking_qoute->price = 0.00;
                    $booking_qoute->hours = 0;
                    $booking_qoute->status = 'pending';
                    $booking_qoute->save();
                    $data['driver'] = User::find($booking_qoute->driver_id);
                    $data['booking'] = $booking;
                    if(env('MAILS_ENABLE')){
                        Mail::to($data['driver']->email)->send(new DriverRequestMail($data));
                    }
                }

             }
            /////////


            if(!empty($booking)){
                $booking = Booking::find($id);
                $data['user'] = User::find($booking->sender_id);
                $data['booking'] = $booking;
               if(env('MAILS_ENABLE')){
                    Mail::to($data['user']->email)->send(new CustomerRequestUpdateMail($data));
                }
            }

            if(!empty($booking)){

                $status = "1";
                $message = "Booking has been updated successfully";

            }
            else
            {
                $status = "0";
                $message = "Booking could not be updated";
            }

        }

         echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


    public function get_drivers(Request $request){

        $truck_id = $request->truck_id;
        $drivers = User::whereIn('id',function($query) use ($truck_id){
            $query->select('user_id')
                    ->from('driver_details')
                    ->where('truck_type_id',$truck_id);
        })->where('role_id',2)->get();

        $options = view('admin.bookings.drivers',compact('drivers'))->render();
        return response()->json(['options' => $options],200);
    }

    public function getbookingList(Request $request){

        $sqlBuilder = Booking::join('users as customers','customers.id','=','bookings.sender_id')
        ->join('categories','categories.id','=','bookings.category_id')
        ->leftJoin('users as companies','companies.id','=','bookings.company_id')
        ->select([
            DB::raw('bookings.id::text as id'),
            DB::raw('bookings.booking_number::text as booking_number'),
            DB::raw('customers.name::text as customer_name'),
            DB::raw('companies.name::text as company_name'),
            DB::raw('categories.id::text as category_id'),
            DB::raw('categories.name::text as category_name'),
            DB::raw('bookings.status::text as booking_status'),
            DB::raw('bookings.qouted_amount::text as qouted_amount'),
            DB::raw('bookings.comission_amount::text as comission_amount'),
            DB::raw('bookings.is_paid::text as is_paid'),
            DB::raw('bookings.created_at::text as created_at'),
        ])->where('admin_response','approved')->where('bookings.status','!=','delivered')->orderBy('bookings.id','DESC');

        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('booking_number', function ($data) {
            $html = '';
            $html .= $data['booking_number'];
            return $html;
        });

        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y h:i A');
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
                $status = 'Company Quoted';
                $status_color = 'warning';
            }
            else if($data['booking_status'] == 'customer_accepted'){
                $status = 'Customer Quote Accepted';
                $status_color = 'success';
            }
            else if($data['booking_status'] == 'journey_started'){
                $status = 'Journey Started';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'item_collected'){
                $status = 'Item Collected';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'on_the_way'){
                $status = 'On The Way';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'delivered'){
                $status = 'Delivered';
                $status_color = 'primary';
            }
            $statuses = ['customer_requested','company_qouted','customer_accepted','item_collected','on_the_way','delivered'];

            $html = '';
            if (get_user_permission('bookings', 'u')) {

                $html = '<span class="badge badge-'.$status_color.'">'.ucwords($status).'</span>';

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
                $html = '<span class="badge badge-'.$status_color.'">'.ucwords($status).'</span>';
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
                   href="' . route('booking.qoutes', ['id' => encrypt($data['id']), 'type'=>'InProgress']) . '"><i
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

    public function change_status($id,$status){

        Booking::where('id',$id)->update(['status' => $status]);

        $booking = Booking::find($id);

        if($booking->status == 'pending'){
            $status_booking = 'request_created';
        }
        else{
            $status_booking = $booking->status;
        }

        BookingStatusTracking::updateOrCreate(['booking_id' => $booking->id,'status_tracking' => $status_booking],['status_tracking' => $status_booking]);

        return redirect()->back();
    }

    public function payment_status($id,$status){

        Booking::where('id',$id)->update(['is_paid' => ($status == 'paid')?'yes':'no']);
    if($status == 'paid'){

        $data = Booking::where('id',$id)->first();
        $sender_id = $data->sender_id;
        $qouted_amount = $data->qouted_amount;
        $comission_amount = $data->comission_amount;

        $result_com = ($comission_amount / 100) * $qouted_amount;


        $total_amt = $qouted_amount + $result_com;

        $wallet = Wallet::where('user_id',$sender_id)->first();

        $wall_amt = $wallet->amount;

        $upd_amount = $wall_amt - $total_amt;


        $value = [
            'amount'  => $upd_amount ,
        ];
        DB::table('user_wallets')->where('user_id',$sender_id)->update($value);



        $auth_id = Auth::user()->id;


        $wallet_new = new user_wallet_transactions();
        $wallet_new->user_wallet_id = $sender_id;
        $wallet_new->amount = $total_amt;
        $wallet_new->type = 'debit';
        $wallet_new->created_by = $auth_id;
        $wallet_new->save();





        return redirect()->back();
    }
        else{
            return redirect()->back();
        }
    }

    public function booking_qoutes($id,$type){
        $id = decrypt($id);

        $booking = Booking::find($id);

        $page_heading = $type;
        $mode = "Request Quotes against ".$booking->booking_number;;
        $exist_drivers = $booking->booking_qoutes->pluck('driver_id')->toArray();

        $drivers = User::join('driver_details' ,'driver_details.user_id','=','users.id')->where('truck_type_id',$booking->truck_type_id)->whereNotIn('users.id',$exist_drivers)->where('users.status','active')->get();

        return view('admin.bookings.qoutes', compact('mode', 'page_heading','id','drivers'));
    }

    public function getBookingQouteList($id){

        $sqlBuilder = BookingQoute::join('users as companies','companies.id','=','booking_qoutes.company_id')->select([
            'booking_qoutes.id as id',
            'booking_qoutes.booking_id as booking_id',
            'companies.name as company_name',
            'booking_qoutes.price as qouted_amount',
            'booking_qoutes.hours as hours',
            'booking_qoutes.status as qoute_status',
            'booking_qoutes.comission_amount as comission_amount',
            'booking_qoutes.created_at as created_at',
            'booking_qoutes.is_admin_approved as is_admin_approved',
        ])->where('booking_qoutes.booking_id',$id)->orderBy('booking_qoutes.id','DESC');

        $dt = new Datatables(new LaravelAdapter);
        $dt->query($sqlBuilder);


        $dt->edit('qouted_amount', function ($data) {
            return $data['qouted_amount'] ?? 0.00;
        });

        $dt->edit('comission_amount', function ($data) {
            return $data['comission_amount'] ?? 0.00;
        });

        $dt->edit('qoute_status', function ($data) {
            $status = '';
            if($data['qoute_status'] == 'pending'){
                $status = '<span class="badge badge-secondary">PENDING</span>';
            }
            else if($data['qoute_status'] == 'qouted'){

                $status = '<span class="badge badge-success">QOUTED</span>';
            }
            else if($data['qoute_status'] == 'accepted'){
                $status = '<span class="badge badge-info">ACCEPTED</span>';
            }
            else if($data['qoute_status'] == 'rejected'){
                $status = '<span class="badge badge-primary">REJECTED</span>';
            }
            return $status;
        });


        $dt->add('check_all', function ($data) {
            if($data['qoute_status'] == 'qouted'){

                if($data['is_admin_approved'] == 'yes'){
                    $html = '<input type = "checkbox" name = "ids[]" value = "'.$data['id'].'" class = "checked" checked = "checked" onclick="this.checked=!this.checked;">';
                }else{
                    $html = '<input type = "checkbox" name = "ids[]" value = "'.$data['id'].'" class = "check_all">';
                }

            }else{
                $html = '<input type = "checkbox" name = "ids[]" value = "" disabled = "disabled">';
            }

            return $html;
        });

        return $dt->generate();
    }


    public function approve_qoutes(Request $request){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('bookings.list');
        $rules = [
            'ids' => 'required',
            'booking_id' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else{

            $bool = BookingQoute::whereIn('id',$request->ids)->update(['is_admin_approved' => 'yes']);

            if($bool){
                $booking_bool = Booking::where('id',$request->booking_id)->update(['status' => 'qouted','admin_response' => 'approved_by_admin']);

                if($booking_bool){
                    $booking = Booking::find($request->booking_id);
                    $data['user'] = User::find($booking->sender_id);
                    $data['booking'] = $booking;
                    if(env('MAILS_ENABLE')){
                        Mail::to($data['user']->email)->send(new DriverQoutedRequest($data));
                    }
                    $status     = "1";
                    $message    = "The Following Quotes have been approved and sent to customer";

                }
                else{
                    $status     = "0";
                    $message    = "Sorry The Following Quotes could not approved";
                }

            }
            else{
                $status     = "0";
                $message    = "Sorry The Following Quotes could not approved";
            }
        }

         echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function add_commission(Request $request){

        $html = '';
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = '';
        $rules = [
            'commission_amount' => 'required',
            'booking_id' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else{

            $booking = Booking::find($request->booking_id);
            $booking->comission_amount = $request->commission_amount;
            $booking->save();

            $total_amount = get_total_calculate($booking->qouted_amount,$booking->comission_amount);
            $booking->total_amount = $total_amount;
            $booking->save();

            if($booking){

                $status     = "1";
                $message    = "The commission amount has been added to the booking";

                $html .= (number_format($request->commission_amount,3) ?? number_format(0));
                $html .= '<i data-id = "'.$request->booking_id.'" class = "edit-commission fa fa-pencil float-right"></i>';
            }
            else{
                $status     = "0";
                $message    = "Sorry! The following commission amount could not added";
            }

        }
        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data,'html' => $html]);
    }


    public function assign_drvivers(Request $request,$id){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = '';
        $rules = [
            'drivers' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();

        }
        else{

            $booking = Booking::find($id);
            $booking->admin_response = 'ask_for_qoute';
            $booking->save();

            if(!empty($booking) && count($request->drivers) > 0){

                foreach($request->drivers as $driver){

                    $booking_qoute = new BookingQoute();
                    $booking_qoute->booking_id = $booking->id;
                    $booking_qoute->driver_id = $driver;
                    $booking_qoute->price = 0.00;
                    $booking_qoute->hours = 0;
                    $booking_qoute->status = 'pending';
                    $booking_qoute->save();
                    $data['driver'] = User::find($booking_qoute->driver_id);
                    $data['booking'] = $booking;
                    if(env('MAILS_ENABLE')){
                        Mail::to($data['driver']->email)->send(new DriverRequestMail($data));
                    }
                }

            }

            if(!empty($booking) && !empty($booking_qoute)){

                $status = "1";
                $message = "Drivers assigned successfully";

            }
            else
            {
                $status = "0";
                $message = "Drivers could not be assigned";
            }

        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function get_booking_charges(Request $request){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = '';

        $booking = Booking::find($request->booking_id);

        if(empty($booking)){
            $status = "0";
            $o_data['html'] = "Booking Not Found";
        }else{
            $status = "1";
            $o_data['booking_number'] = $booking->booking_number;
            $o_data['html'] = view('admin.bookings.charges',compact('booking'))->render();
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


    public function add_booking_charges(Request $request){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = '';
        $rules = [
            'booking_id'    => 'required',
            'qouted_amount' => 'required|numeric',
            'commission' => 'required|numeric',
            'shipping_charges' => 'required|numeric',
            'cost_of_truck' => 'required|numeric',
            'border_charges' => 'required|numeric',
            'custom_charges' => 'required|numeric',
            'waiting_charges' => 'required|numeric'
        ];


        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else{

            $booking = Booking::find($request->booking_id);

            if(empty($booking)){
                $status = "0";
                $o_data['html'] = "Booking Not Found";
            }else{
                $status = "1";
                $booking->comission_amount = $request->commission;
                $booking->shipping_charges = $request->shipping_charges;
                $booking->cost_of_truck = $request->cost_of_truck;
                $booking->border_charges = $request->border_charges;
                $booking->custom_charges = $request->custom_charges;
                $booking->waiting_charges = $request->waiting_charges;
                $booking->save();

                for($i = 0;isset($request->charges_name[$i]) && isset($request->amount[$i]);$i++){

                  $new_charge = new BookingAdditionalCharge();
                  $new_charge->booking_id = $booking->id;
                  $new_charge->charges_name = $request->charges_name[$i];
                  $new_charge->charges_amount = $request->amount[$i];
                  $new_charge->save();

                }

                if($request->old_charges_name != null && count($request->old_charges_name) > 0){
                    foreach($request->old_charges_name as $key => $value){
                        if(isset($request->old_charges_name[$key]) && isset($request->old_amount[$key])){

                            $old_charge = BookingAdditionalCharge::find($key);
                            $old_charge->booking_id = $booking->id;
                            $old_charge->charges_name = $request->old_charges_name[$key];
                            $old_charge->charges_amount = $request->old_amount[$key];
                            $old_charge->save();

                        }
                    }
                }
                $booking_additional_charges = BookingAdditionalCharge::where('booking_id',$booking->id)->sum('charges_amount');
                $total_amount = get_total_calculate($booking->qouted_amount,$booking->comission_amount);
                $grand_total = ($total_amount + $booking->shipping_charges + $booking->cost_of_truck + $booking->border_charges + $booking->custom_charges + $booking->waiting_charges + $booking_additional_charges);
                $booking->total_amount = $grand_total;
                $booking->save();

                $o_data['html'] = view('admin.bookings.charges',compact('booking'))->render();
            }

        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function remove_booking_charges(Request $request){

        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = '';
        $rules = [
            'id'    => 'required',
        ];


        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else{

            $charges = BookingAdditionalCharge::find($request->id);

            if(empty($charges)){
                $status = "0";
                $o_data['html'] = "Booking Charges Not Found";
            }else{
                $status = "1";
                $charges->delete();

                $booking = Booking::find($charges->booking_id);

                $booking_additional_charges = BookingAdditionalCharge::where('booking_id',$booking->id)->sum('charges_amount');
                $total_amount = get_total_calculate($booking->qouted_amount,$booking->comission_amount);
                $grand_total = ($total_amount + $booking->shipping_charges + $booking->cost_of_truck + $booking->border_charges + $booking->custom_charges + $booking->waiting_charges + $booking_additional_charges);
                $booking->total_amount = $grand_total;
                $booking->save();

                $o_data['html'] = view('admin.bookings.charges',compact('booking'))->render();
            }

        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function index_new(REQUEST $request)
    {  
        
        $page_heading = "Received Deliveries";
        if($request->type == 1)
        {
        $page_heading = "Pickup Deliveries";    
        }
        if($request->type == 2)
        {
        $page_heading = "Drop off Deliveries ";    
        }
        $mode = "List";
        return view('admin.bookings.new', compact('mode', 'page_heading'));
    }

    public function index_pickup_orders(REQUEST $request)
    {
        $page_heading = "Pickup Deliveries";
        $mode = "List";
        return view('admin.bookings.pickup_request', compact('mode', 'page_heading'));
    }

    public function index_delivery_orders(REQUEST $request)
    {
        $page_heading = "Drop off Deliveries";
        $mode = "List";
        return view('admin.bookings.delivery_request', compact('mode', 'page_heading'));
    }

    public function index_delivered(REQUEST $request)
    {
        $page_heading = "Drop off Deliveries";
        $mode = "List";
        return view('admin.bookings.delivered', compact('mode', 'page_heading'));
    }



    public function index_rejected(REQUEST $request)
    {
        $page_heading = "Rejcted Requests";
        $mode = "List";
        return view('admin.bookings.rejected', compact('mode', 'page_heading'));
    }

    public function index_shipped(REQUEST $request)
    {
        $page_heading = "Shipped Requests";
        $mode = "List";
        return view('admin.bookings.shipped', compact('mode', 'page_heading'));
    }


    public function getnewbookingList($status){

        $sqlBuilder = Booking::join('users as customers','customers.id','=','bookings.sender_id')->join('categories','categories.id','=','bookings.category_id')->
        select([
            DB::raw('bookings.id::text as id'),
            DB::raw('bookings.booking_number::text as booking_number'),
            DB::raw('categories.id::text as category_id'),
            DB::raw('categories.name::text as category_name'),
            DB::raw('customers.name::text as customer_name'),
            DB::raw('bookings.created_at::text as created_at'),
            DB::raw('bookings.status::text as booking_status'),
            DB::raw('bookings.admin_response::text as admin_response'),
        ])->addSelect(['total_companies' => CompanyCategory::selectRaw('count(*) as total_categories')
            ->whereColumn('categories.id', 'company_categories.category_id')])->where('admin_response',$status)->orderBy('bookings.id','DESC');//
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);


        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y h:i A');
        });

        $dt->edit('category_name', function ($data) {
             $html = '';
             $html .= '<b>'.$data['category_name'].'</b>';
             $html .= '<br><small> Total Companies ('.$data['total_companies'].') </small>';
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
            if($data['admin_response'] == 'pending' && $data['total_companies'] > 0){

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


    public function getdeliveredbookingList(){

        $sqlBuilder = Booking::join('users as customers','customers.id','=','bookings.sender_id')->join('categories','categories.id','=','bookings.category_id')
        ->leftJoin('users as companies','companies.id','=','bookings.company_id')
        ->select([
            DB::raw('bookings.id::text as id'),
            DB::raw('bookings.booking_number::text as booking_number'),
            DB::raw('customers.name::text as customer_name'),
            DB::raw('companies.name::text as company_name'),
            DB::raw('categories.id::text as category_id'),
            DB::raw('categories.name::text as category_name'),
            DB::raw('bookings.status::text as booking_status'),
            DB::raw('bookings.qouted_amount::text as qouted_amount'),
            DB::raw('bookings.comission_amount::text as comission_amount'),
            DB::raw('bookings.is_paid::text as is_paid'),
            DB::raw('bookings.created_at::text as created_at'),
        ])->where('admin_response','approved')->where('bookings.status','delivered')->orderBy('bookings.id','DESC');//
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('booking_number', function ($data) {
            $html = '';
            $html .= $data['booking_number'];
            return $html;
        });

        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y h:i A');
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
                $status = 'Company Quoted';
                $status_color = 'warning';
            }
            else if($data['booking_status'] == 'customer_accepted'){
                $status = 'Customer Quote Accepted';
                $status_color = 'success';
            }
            else if($data['booking_status'] == 'journey_started'){
                $status = 'Journey Started';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'item_collected'){
                $status = 'Item Collected';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'on_the_way'){
                $status = 'On The Way';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'delivered'){
                $status = 'Delivered';
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
                   href="' . route('booking.qoutes', ['id' => encrypt($data['id']), 'type'=>'Delivery']) . '"><i
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

    public function booking_approve($id){

        $id = decrypt($id);
        $booking = Booking::find($id);

        if(!empty($booking)){
            $booking->admin_response = 'approved';
            $booking->save();
            if($booking){

                $companies = CompanyCategory::where('category_id',$booking->category_id)->pluck('company_id');

                if(!empty($booking) && count($companies) > 0){

                    $companies = Company::whereIn('id',$companies->toArray())->pluck('user_id');


                    foreach($companies as $company_id){

                        $booking_qoute = new BookingQoute();
                        $booking_qoute->booking_id = $booking->id;
                        $booking_qoute->company_id = $company_id;
                        $booking_qoute->price = 0.00;
                        $booking_qoute->hours = 0;
                        $booking_qoute->status = 'pending';
                        $booking_qoute->save();
                        $data['company'] = User::find($booking_qoute->company_id);
                        $data['booking'] = $booking;
                        if(env('MAILS_ENABLE')){
                            Mail::to($data['company']->email)->send(new DriverRequestMail($data));
                        }
                    }

                }

                session()->flash('success','Customer Request Approved Successfully');
            }
            else{
                session()->flash('error','Customer Request Could Not Approve');
            }
        }
        else{
            session()->flash('error','Customer Request Could Not Approve');
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

    public function create_new_request($id ="")
    {
        $datamain = [];
        if($id)
        {
            $datamain =  BookingPickUpOrder::find($id);
            $datamain->dropoff = BookingDropOffOrder::where('pick_up_id',$id)->first();
            $datamain->images = RequestImages::where('request_id',$id)->get();
            
        }
        $settings = Settings::find(1);
        $page_heading = "New Request Create";
        $mode = "create";
        $id = "";
        $name = "";
        $cost = "";
        $status = "1";
        $route_back = route('bookings.list.new');
        // $customers = Customer::all();
        $customers = User::where('status','active')->where('role_id',3)->get();
        $categories = Category::all();
        $sizes = Size::all();
        $cares = Care::all();
        $delivery_type = "";
        $customer_id = "";
        $category_id = "";
        $size_id = "";
        $delivery_type_selected = "";
        $drivers =  User::select('users.id','name')->join('roles','roles.id','=','users.role_id')
        ->join('driver_details','driver_details.user_id','=','users.id')->whereNotIn('user_id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id','=',2)->get();
      
        return view("admin.bookings.create_new_request", compact('page_heading', 'mode', 'id', 'name', 'cost', 'status', 'route_back', 'categories', 'sizes', 'delivery_type', 'category_id', 'size_id', 'delivery_type_selected', 'customers', 'customer_id', 'cares','datamain','drivers','settings'));
    }

    public function view_request($id ="")
    {
        $datamain = [];
        if($id)
        {
            $datamain =  BookingPickUpOrder::find($id);
            $datamain->dropoff = BookingDropOffOrder::where('pick_up_id',$id)->first();
            $datamain->images = RequestImages::where('request_id',$id)->get();
            
        }
        $page_heading = "View Request";
        $mode = "view";
        $id = "";
        $name = "";
        $cost = "";
        $status = "1";
        $route_back = route('bookings.list.new');
        // $customers = Customer::all();
        $customers = User::where('status','active')->where('role_id',3)->get();
        $categories = Category::all();
        $sizes = Size::all();
        $cares = Care::all();
        $delivery_type = "";
        $customer_id = "";
        $category_id = "";
        $size_id = "";
        $delivery_type_selected = "";
        $drivers =  User::select('users.id','name')->join('roles','roles.id','=','users.role_id')
        ->join('driver_details','driver_details.user_id','=','users.id')->whereNotIn('user_id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id','=',2)->get();
      
        return view("admin.bookings.request_view", compact('page_heading', 'mode', 'id', 'name', 'cost', 'status', 'route_back', 'categories', 'sizes', 'delivery_type', 'category_id', 'size_id', 'delivery_type_selected', 'customers', 'customer_id', 'cares','datamain','drivers'));
    }
    public function view_pickup_request($id ="")
    {
        $datamain = [];
        if($id)
        {
            $datamain =  BookingPickUpOrder::find($id);
            $datamain->dropoff = BookingDropOffOrder::where('pick_up_id',$id)->first();
            $datamain->images = RequestImages::where('request_id',$id)->get();
            
        }
        $page_heading = "View Pickup Request";
        $mode = "view";
        $id = "";
        $name = "";
        $cost = "";
        $status = "1";
        $route_back = route('bookings.list.new');
        // $customers = Customer::all();
        $customers = User::where('status','active')->where('role_id',3)->get();
        $categories = Category::all();
        $sizes = Size::all();
        $cares = Care::all();
        $delivery_type = "";
        $customer_id = "";
        $category_id = "";
        $size_id = "";
        $delivery_type_selected = "";
        $drivers =  User::select('users.id','name')->join('roles','roles.id','=','users.role_id')
        ->join('driver_details','driver_details.user_id','=','users.id')->whereNotIn('user_id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id','=',2)->get();
      
        return view("admin.bookings.request_view_pickup", compact('page_heading', 'mode', 'id', 'name', 'cost', 'status', 'route_back', 'categories', 'sizes', 'delivery_type', 'category_id', 'size_id', 'delivery_type_selected', 'customers', 'customer_id', 'cares','datamain','drivers'));
    }


    public function view_delivery_request($id ="")
    {
        $datamain = [];
        if($id)
        {
            $datamain =  BookingPickUpOrder::with('customer_details')->find($id);
            $datamain->dropoff = BookingDropOffOrder::where('pick_up_id',$id)->first();
            $datamain->images = RequestImages::where('request_id',$id)->get();
            
        }
        $page_heading = "View Delivery Request";
        $mode = "view ";
        $id = "";
        $name = "";
        $cost = "";
        $status = "1";
        $route_back = route('bookings.list.new');
        // $customers = Customer::all();
        $customers = User::where('status','active')->where('role_id',3)->get();
        $categories = Category::all();
        $sizes = Size::all();
        $cares = Care::all();
        $delivery_type = "";
        $customer_id = "";
        $category_id = "";
        $size_id = "";
        $delivery_type_selected = "";
        $drivers =  User::select('users.id','name')->join('roles','roles.id','=','users.role_id')
        ->join('driver_details','driver_details.user_id','=','users.id')->whereNotIn('user_id', function($query) {
            $query->select('user_id')
                  ->from('blacklists')
                  ->whereColumn('users.id','=','blacklists.user_id');
        })->where('role_id','=',2)->get();
      
        return view("admin.bookings.request_view_delivery", compact('page_heading', 'mode', 'id', 'name', 'cost', 'status', 'route_back', 'categories', 'sizes', 'delivery_type', 'category_id', 'size_id', 'delivery_type_selected', 'customers', 'customer_id', 'cares','datamain','drivers'));
    }


    public function create_new_request_store(Request $request)
    {

       
        $image_path = "";
        // if (!empty($request->images)) 
        // {
        //     $file = $request->file('image');
        //     $extension = $file->getClientOriginalExtension();
        //     $filename = time() . '.' . $extension;
        //     $file->move(public_path('uploads/'), $filename);
        //     $image_path = 'public/uploads/' . $filename;
        // }
        $id = $request->id;
            
      
        $ins = 
        [
            'customer_id' => $request->customer_id,
            'category_id' => $request->category_id,
            'location' => $request->pu_location,
            'landmark' => $request->pu_landmark??'',
            'contact_person' => $request->pu_contact_person,
            'mobile_no' => $request->pu_mob_no,
            'instruction' => $request->instruction,
            'description' => $request->description,
            'size_id' => $request->size_id,
            'care_id' => $request->care_id??0,
            'date' => $request->date??'',
            'time' => $request->time??'',
            'delivery_type' => $request->delivery_type,
            'dail_code' => $request->do_dail_code,
            'updated_at' => gmdate('Y-m-d H:i:s'),
            'cost' => $request->cost_input,
            'service_price' => $request->service_price_input,
            'tax' => $request->tax_input,
            'grand_total' => $request->grand_total_input,
            'pickup_driver' => $request->pickup_driver??0,
            'delivery_driver' => $request->delivery_driver??0,
            'booking_status' => $request->booking_status??0,
            'payment_type'   => $request->payment_type??0,
            'payment_status' => 1,
            'po_latitude' => $request->po_latitude,
            'po_longitude' => $request->po_longitude,
            'do_latitude' => $request->do_latitude,
            'do_longitude' => $request->do_longitude,
        ];
        
        if($request->booking_status == 4 && !empty($request->delivery_driver))
        {
            $ins['booking_status'] = 5;
        }
        if (!empty($image_path)) 
        {
            $ins['image_path'] = $image_path;
        }
        if(!empty($id))
        {
            $pick_up_insert = BookingPickUpOrder::where('id',$id)->update($ins);
            $last_pick_up_id  = $id;
        }
        else
        {
            $ins['created_at'] = gmdate('Y-m-d H:i:s');
            $pick_up_insert = BookingPickUpOrder::create($ins);
            $last_pick_up_id  = $pick_up_insert->id;
            $pick_up_insert->order_number = "P-".date('Ym').$last_pick_up_id;
            $pick_up_insert->delivery_order_number = "D-".date('Ym').$last_pick_up_id;
            $pick_up_insert->save();
        }
        

        

        $do_insert = 
        [
            'order_number'=> '',
            'pick_up_id' => $last_pick_up_id,
            'customer_id' => $request->customer_id,
            'category_id' => $request->category_id,
            'location' => $request->do_location,
            'landmark' => $request->do_landmark??'',
            'contact_person' => $request->do_contact_person,
            'mobile_no' => $request->do_mob_no,
            'instruction' => $request->instruction,
            'description' => $request->description,
            'size_id' => $request->size_id,
            'care_id' => $request->care_id??0,
            'date' => $request->date??'',
            'time' => $request->time??'',
            'delivery_type' => $request->delivery_type,
            'dail_code' => $request->do_dail_code,
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ];

       
        if(!empty($id))
        {
            $drop_off_insert = BookingDropOffOrder::where('pick_up_id',$last_pick_up_id)->update($do_insert);
            //$last_drop_off_id  = $drop_off_insert->id;
           
        }
        else
        {
            $do_insert['created_at'] = gmdate('Y-m-d H:i:s');
        $drop_off_insert = BookingDropOffOrder::create($do_insert);
        $last_drop_off_id  = $drop_off_insert->id;
        $drop_off_insert->order_number = "AWB-".date('Ym').$last_drop_off_id;
        $drop_off_insert->save();
        }
        
        

        $banners = $request->file("banners");

                $banner_images = [];
                if ($banners) {
                foreach ($banners as $ikey => $img) {

                    if ($file = $img) {
                        $dir = config('global.upload_path') . "/" . config('global.request_images_upload_dir');
                        $file_name = time() . uniqid() . "." . $file->getClientOriginalExtension();
                        $file->storeAs(config('global.request_images_upload_dir'), $file_name, config('global.upload_bucket'));

                        $gameimages = new RequestImages;
                        $gameimages->request_id = $last_pick_up_id;
                        $gameimages->image = $file_name;
                        $gameimages->save();

                    }


                }
               }
               $notification_send = 0;
         $dataold = BookingPickUpOrder::find($last_pick_up_id);
               
        if(!empty($dataold))
        {
            if(empty($request->delivery_driver))
            {
                $driver = $request->pickup_driver;
                $notification_send = 1;
            }

            elseif($request->delivery_driver != $request->pickup_driver)
            {
                $driver = $request->delivery_driver;
                $notification_send = 1;
            }
        }
        else
        {
            $driver = $request->pickup_driver;
            $notification_send = 1;
        }
            
       if($notification_send == 1)
       {

        

               $user = User::find($driver);

             
        $order_no = $dataold->order_number;
        $order_id = $dataold->id;
        if($dataold->booking_status > 4)
        {
        $order_no = $dataold->delivery_order_number;
        }
       
        $title = $order_no;
        $status = $dataold->booking_status;

        $description = "Got assigned a new order";
        $notification_id = time();
        $ntype = 'new_order_assigned';
        if (!empty($user->firebase_user_key)) {
           
            $notification_data["Notifications/" . $user->firebase_user_key . "/" . $notification_id] = [
                "title" => $title,
                "description" => $description,
                "notificationType" => $ntype,
                "createdAt" => gmdate("d-m-Y H:i:s", $notification_id),
                "orderId" => (string) $order_id,
                "status" => (string) $status,
                "url" => "",
                "imageURL" => '',
                "read" => "0",
                "seen" => "0",
            ];
            $this->database->getReference()->update($notification_data);
        }

        if (!empty($user->user_device_token)) {
           
            $res = send_single_notification(
                $user->user_device_token,
                [
                    "title" => $title,
                    "body" => $description,
                    "icon" => 'myicon',
                    "sound" => 'default',
                    "click_action" => "EcomNotification"
                ],
                [
                    "type" => $ntype,
                    "notificationID" => $notification_id,
                    "orderId" => (string) $order_id,
                    "status" => (string) $status,
                    "imageURL" => "",
                ]
            );
         
        }
    }
        

        // Check if request has an ID for updating an existing record
        // if ($request->has('id')) 
        // {
        //     $pick_up = BookingPickUpOrder::find($request->id);
        //     if ($pick_up) 
        //     {
        //         if (!empty($image_path)) 
        //         {
        //             $ins['image_path'] = $image_path;
        //         }
        //         $pick_up->update($ins);
        //         $message = "Booking updated successfully";
        //     } 
        //     else 
        //     {
        //         $message = "Booking not found for updating";
        //     }
        // } 
        // else 
        // {
        //     // Otherwise, create a new record
        //     $ins['created_at'] = gmdate('Y-m-d H:i:s');
        //     if (!empty($image_path)) 
        //     {
        //         $ins['image_path'] = $image_path;
        //     }
        //     $pick_up_insert = BookingPickUpOrder::create($ins);


            // $message = "Added successfully";
        // }

        return redirect()->route("bookings.list.new");
    }

    public function request_update(Request $request)
    {
        $image_path = "";
        
        $id = $request->id;
        $ins = 
        [
            'booking_status' => $request->booking_status??0,
        ];

        
        if(!empty($id))
        {
            $pick_up_insert = BookingPickUpOrder::where('id',$id)->update($ins);
            $last_pick_up_id  = $id;
        }



        return redirect()->route("bookings.list.new");
    }

    public function create_new_request_get_costing(Request $request)
    {
        $request->category_id;
        $request->size_id;
        $costing = Costing::select("cost")->where('category_id', $request->category_id)
        ->where('size_id', $request->size_id)->where('delivery_type',$request->delivery_type)->first();
       
        return $costing->cost??0;
    }


    public function get_new_request_list(Request $request)
    {
        $sqlBuilder = BookingPickUpOrder::select([
            DB::raw('booking_pick_up_orders.id::text as id'),
            DB::raw('booking_pick_up_orders.order_number::text as order_number'),
            DB::raw('booking_pick_up_orders.delivery_order_number::text as delivery_order_number'),
            DB::raw('booking_pick_up_orders.created_at::text as created_at'),
            DB::raw('categories.name::text as category_name'),
            DB::raw('users.name::text as customer_name'),
            DB::raw('booking_pick_up_orders.booking_status::text as booking_status'),
        ])
            ->leftJoin('categories', 'booking_pick_up_orders.category_id', '=', 'categories.id')
            ->leftJoin('users', 'booking_pick_up_orders.customer_id', '=', 'users.id')
            ->where('payment_status',1)->orderBy('booking_pick_up_orders.id', 'DESC');
            if(!empty($request->p_driver_id))
            {
                $sqlBuilder = $sqlBuilder->where('pickup_driver',$request->p_driver_id);
            }
            if(!empty($request->d_driver_id))
            {
                $sqlBuilder = $sqlBuilder->where('delivery_driver',$request->d_driver_id);
            }

            if(!empty($request->cus_id))
            {
                $sqlBuilder = $sqlBuilder->where('customer_id',$request->cus_id);
            }

            if(!empty($request->status))
            {
                $sqlBuilder = $sqlBuilder->where('booking_status',$request->status);
            }

            if(!empty($request->type))
            {
                if($request->type == 1)
                {
                    $sqlBuilder = $sqlBuilder->where('booking_status','<=', 8);
                }
                if($request->type == 2)
                {
                    $sqlBuilder = $sqlBuilder->where('booking_status','>=', 4);
                }
                
            }
            

        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('created_at', function ($data) 
        {
            return (new Carbon($data['created_at']))->format('d/m/y h:i A');
        });

        $dt->add('qr_code', function ($data) 
        {
            return QrCode::generate($data['order_number']);
        });

        $dt->edit('booking_status', function ($data) use ($request)
        {   
            $status = booking_status($data['booking_status']);
            if($request->type == 1)
            {
                if($data['booking_status'] >= 4)
                {
                    $status = booking_status(4);
                }
            }
            
            return $status;
        });

        if($request->type == 2)
                {
                    $dt->add('qr_code', function ($data) 
                    {
                        return QrCode::generate($data['delivery_order_number']??$data['order_number']);
                    });
        $dt->edit('order_number', function ($data) 
        {
            
            return $data['delivery_order_number'];
        });
        }

        // $dt->edit('booking_pick_up_orders', function ($data) 
        // {
        //     return Config('global.default_currency_code').' '.$data['cost'];
        // });

        // $dt->edit('status_text', function ($data) {
        //     $statusTextHtml = '';
        //     if ($data["status"] == 'active') {
        //         $statusTextHtml = '<div class="ticket active">
        //         <i class="fas fa-check-circle text-success"></i> Active </div>';
        //     } else {
        //         $statusTextHtml = '<div class="ticket disabled">
        //         <i class="fas fa-times-circle text-danger"></i> Disabled
        //         </div>';
        //     }
        //     return $statusTextHtml;
        // });

        // $dt->edit('status', function ($data) {
        //     if (get_user_permission('Costings', 'u')) {
        //         $checked = ($data["status"] == 'active') ? "checked" : "";
        //         $html = '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
        //                 <input type="checkbox" data-role="active-switch"
        //                     data-href="' . route('costings.change_status', ['id' => encrypt($data['id'])]) . '"
        //                     ' . $checked . ' >
        //                 <span class="slider round"></span>
        //             </label>';
        //     } else {
        //         $checked = ($data["status"] == 'active') ? "Active" : "InActive";
        //         $class = ($data["status"] == 'active') ? "badge-success" : "badge-danger";
        //         $html = '<span class="badge ' . $class . '" ' . $checked . ' </span>';
        //     }
        //     return $html;
        // });


        $dt->add('action', function ($data) 
        {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                if (get_user_permission('Costings', 'u')) 
                {
                    $html .= '<a class="dropdown-item" href="' . route('admin.bookings.view_request', ['id' => $data['id']]) . '"><i class="flaticon-pencil-1"></i> View</a>';
                }
            if (get_user_permission('Costings', 'u')) 
            {
                $html .= '<a class="dropdown-item" href="' . route('admin.bookings.edit_request', ['id' => $data['id']]) . '"><i class="flaticon-pencil-1"></i> Edit</a>';
            }
           
            // if (get_user_permission('categories', 'd')) {
            //    $html .= '<a class="dropdown-item"
            //        href="' . route('categories.destroy', ['id' => encrypt($data['id'])]) . '"><i
            //    class="bx bxs-truck"></i> Delete</a>';
            // }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function pickup_request_list(Request $request)
    {
        $sqlBuilder = BookingPickUpOrder::select([
            DB::raw('booking_pick_up_orders.id::text as id'),
            DB::raw('booking_pick_up_orders.order_number::text as order_number'),
            DB::raw('booking_pick_up_orders.created_at::text as created_at'),
            DB::raw('categories.name::text as category_name'),
            DB::raw('users.name::text as customer_name'),
            DB::raw('booking_pick_up_orders.booking_status::text as booking_status'),
        ])
            ->leftJoin('categories', 'booking_pick_up_orders.category_id', '=', 'categories.id')
            ->leftJoin('users', 'booking_pick_up_orders.customer_id', '=', 'users.id')
            ->where('payment_status',1)->orderBy('booking_pick_up_orders.id', 'DESC');
            if(!empty($request->p_driver_id))
            {
                $sqlBuilder = $sqlBuilder->where('pickup_driver',$request->p_driver_id);
            }
            if(!empty($request->d_driver_id))
            {
                $sqlBuilder = $sqlBuilder->where('delivery_driver',$request->d_driver_id);
            }

            if(!empty($request->cus_id))
            {
                $sqlBuilder = $sqlBuilder->where('customer_id',$request->cus_id);
            }

            $sqlBuilder = $sqlBuilder->where('booking_status','<=', 8);
            

        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('created_at', function ($data) 
        {
            return (new Carbon($data['created_at']))->format('d/m/y h:i A');
        });

        $dt->edit('booking_status', function ($data) 
        {   
            $status = booking_status($data['booking_status']);
            if($data['booking_status'] >= 4 )
            {
                $status = booking_status(4);
            }
            return $status;
        });

        $dt->add('qr_code', function ($data) 
        {
            return QrCode::generate($data['order_number']);
        });


        $dt->add('action', function ($data) 
        {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                if (get_user_permission('Costings', 'u')) 
                {
                    $html .= '<a class="dropdown-item" href="' . route('admin.bookings.view_pickup_request', ['id' => $data['id']]) . '"><i class="flaticon-pencil-1"></i> View</a>';
                }
          
           
            // if (get_user_permission('categories', 'd')) {
            //    $html .= '<a class="dropdown-item"
            //        href="' . route('categories.destroy', ['id' => encrypt($data['id'])]) . '"><i
            //    class="bx bxs-truck"></i> Delete</a>';
            // }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function delivery_request_list(Request $request)
    {
      
        $sqlBuilder = BookingPickUpOrder::select([
            DB::raw('booking_pick_up_orders.id::text as id'),
            DB::raw('booking_pick_up_orders.order_number::text as order_number'),
            DB::raw('booking_pick_up_orders.delivery_order_number::text as delivery_order_number'),
            DB::raw('booking_pick_up_orders.created_at::text as created_at'),
            DB::raw('categories.name::text as category_name'),
            DB::raw('users.name::text as customer_name'),
            DB::raw('booking_pick_up_orders.booking_status::text as booking_status'),
        ])
            ->leftJoin('categories', 'booking_pick_up_orders.category_id', '=', 'categories.id')
            ->leftJoin('users', 'booking_pick_up_orders.customer_id', '=', 'users.id')
            ->where('payment_status',1)->orderBy('booking_pick_up_orders.id', 'DESC');
            if(!empty($request->p_driver_id))
            {
                $sqlBuilder = $sqlBuilder->where('pickup_driver',$request->p_driver_id);
            }
            if(!empty($request->d_driver_id))
            {
                $sqlBuilder = $sqlBuilder->where('delivery_driver',$request->d_driver_id);
            }

            if(!empty($request->cus_id))
            {
                $sqlBuilder = $sqlBuilder->where('customer_id',$request->cus_id);
            }

            $sqlBuilder = $sqlBuilder->where('booking_status','>=', 4);
            

        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('created_at', function ($data) 
        {
            return (new Carbon($data['created_at']))->format('d/m/y h:i A');
        });

        $dt->edit('booking_status', function ($data) 
        {
            return booking_status($data['booking_status']);
        });

        $dt->add('qr_code', function ($data) 
        {
            return QrCode::generate($data['delivery_order_number']??$data['order_number']);
        });

        $dt->edit('order_number', function ($data) 
        {
            return $data['delivery_order_number'];
        });


        $dt->add('action', function ($data) 
        {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                if (get_user_permission('Costings', 'u')) 
                {
                    $html .= '<a class="dropdown-item" href="' . route('admin.bookings.view_delivery_request', ['id' => $data['id']]) . '"><i class="flaticon-pencil-1"></i> View</a>';
                }
          
           
            // if (get_user_permission('categories', 'd')) {
            //    $html .= '<a class="dropdown-item"
            //        href="' . route('categories.destroy', ['id' => encrypt($data['id'])]) . '"><i
            //    class="bx bxs-truck"></i> Delete</a>';
            // }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }


    public function delete_image($id)
    { 
        $status = "0";
        $message = "";
        $o_data = [];
        $img =  RequestImages::find($id);
        if ($img) {
            $img->delete();
            $status = "1";
            $message = "Image removed successfully";
        } else {
            $message = "Sorry!.. You cant do this?";
        }

        echo json_encode(['status' => $status, 'message' => $message, 'o_data' => $o_data]);

    }
}
