<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\OrderModel;
use App\Models\UserBooking;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Carbon\Carbon;
use App\Models\DriverBookingRequest;
use App\Models\Category;
use Validator;
use App\Models\VehicleModel;
use App\Models\Manufacturer;
use App\Models\VehicleType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BookingPickUpOrder;

class DriverBookingRequestController extends Controller
{
    //
    public function index($booking_id){
        $page_heading = "Booking Requests";
        $mode="List";
        $booking = UserBooking::find($booking_id);
        
        return view('admin.driver_booking_requests.list',compact('booking_id','mode','page_heading', 'booking'));
    }
    public function create($booking_id='',$id=''){
        
        $page_heading = 'Booking Request';
        $mode="Create";
        $bid_amount  = '';
        $driver_id='';
        $permissions= [];
        $route_back = route('driver_booking_requests.list',$booking_id);
        $booking = UserBooking::find($booking_id);
        $vehicle_type = VehicleType::find($booking->vehicle->category_id);
        $drivers = User::where(['deleted' => 0, 'status' => 'active'])
            ->where('vehicle_type', 'like', '%' . $vehicle_type->model . '%')
            ->get();

        if($id){
            $page_heading = "Booking Request";
            $mode ="Edit";
            $id = decrypt(value: $id);
            $role = DriverBookingRequest::find($id);
            $booking_id = $role->booking_id;
            $bid_amount= $role->bid_amount;
            $driver_id=$role->driver_id;
           
           
        }
       
        return view('admin.driver_booking_requests.create',compact('drivers','driver_id','bid_amount','booking_id','id','mode','page_heading','id','booking_id'));

    }

    public function submit(REQUEST $request){
       
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('driver_booking_requests.list',$request->booking_id);
        $rules = [
            'booking_id' => 'required',
            'bid_amount' => 'required',
            'driver_id'=>'required'
        ];
       

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $booking_id  = $request->booking_id;
            $bid_amount= $request->bid_amount;
            $id         = $request->id;
            $driver_id=$request->driver_id;
            $booking = BookingPickUpOrder::find($booking_id);
           
                if($id){
                    DB::beginTransaction();
                    try{
                        $role   = DriverBookingRequest::find($id);
                        $role->bid_amount    = $bid_amount;
                        $role->booking_id  = $booking_id;
                        $role->driver_id=$driver_id;
                        $role->save();
                       
                        
                        DB::commit();
                        $status = "1";
                        $message = "Request updated Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to update country ".$e->getMessage();
                    }
                }else{
                    DB::beginTransaction();
                    try{
                        $booking = UserBooking::find($booking_id);
                        $booking->booking_status = '1';
                        $booking->save();

                        $role   = new OrderModel();
                        $role->amount    = $bid_amount;
                        $role->booking_id  = $booking_id;
                        $role->vendor_id=$driver_id;
                        $role->status='pending';
                        $role->save();

                        DB::commit();
                        $status = "1";
                        $message = "Request Added Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Failed to create country ".$e->getMessage();
                    }
                }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function get_requests_list($booking_id,Request $request)
    {
        // Initialize the query for country data
        $sqlBuilder = [];
        $booking = UserBooking::find($booking_id);
        $orders = OrderModel::where('booking_id', $booking_id)->orderBy('amount', 'DESC')->get();
        foreach ($orders as $key => $order) {
            $sqlBuilder[$key]['id'] = $order->id;
            $sqlBuilder[$key]['bid_amount'] = $order->amount;
            $sqlBuilder[$key]['status'] = $order->status;
            $sqlBuilder[$key]['booking_id'] = $order->booking_id;
            $sqlBuilder[$key]['driver_name'] = $order->vendor->name;
            $sqlBuilder[$key]['driver_id'] = $order->vendor->id;
            $sqlBuilder[$key]['created_at'] = $order->created_at;
        }
    
        // Custom filtering for search (manual implementation of case-insensitive search)
       // dd($request->input('search')['value']);
    
        // Initialize Datatables
        return DataTables::of($sqlBuilder)
        ->editColumn('created_at', function($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        })
        ->editColumn('status', function($data) {

//            $status = $data['status'] == 'approved' ? 'Accepted' : 'Pending';
            return strtoupper($data['status']);
        })
    
        
    
        // Handle action buttons
        ->addColumn('action', function($data) use($booking) {
            $html = '';
            if ($data['status'] === 'pending' && $booking->status == 1) {
                $html .= '<div class="dropdown custom-dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="flaticon-dot-three"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                $html .= '<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to assign to this driver?"
                        href="'.route('driver_booking_requests.assign_driver', ['pickup_driver'=>$data['driver_id'],'id' => $data['booking_id'],'booking_request_id'=>$data['id']]).'">
                        <i class="flaticon-delete-1"></i> Assign To Driver</a>';
                $html .= '</div></div>';
            }
            return $html;
        })
    
        // Return the generated data
        ->rawColumns([ 'action'])
            ->make(true);
    }
    


    
    
    
    

    

    public function assign_driver($pickup_driver,$id,$booking_request_id) {

        $booking = UserBooking::find($id);
        $booking->booking_status = 2;
        $booking->save();
        $booking_request  = OrderModel::find($booking_request_id);
        $booking_request->status = 'approved';
        $booking_request->save();

        $message = "Driver Assigned to this booking";
        $status = "1";

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
