<?php

namespace App\Http\Controllers\Api\v1\driver;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAdress;
use App\Models\UserFollow;
use App\Models\WalletPaymentReport;
use App\Models\VendorDetailsModel;
use App\Models\Likes;
use App\Models\ProductModel;
use App\Models\Rating;
use App\Models\BookingPickUpOrder;
use App\Models\BookingDropOffOrder;
use App\Models\RequestImages;
use App\Models\Costing;
use App\Models\Settings;
use App\Models\Tracking;
use App\Models\MuteOrders;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\DatabaseRule;
use Illuminate\Contracts\Validation\Rule;
use Kreait\Firebase\Contract\Database;
use Validator;
use Illuminate\Support\Facades\Artisan;
class OrdersController extends Controller
{
    //
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    private function validateAccesToken($access_token)
    {
        $user = User::where(['user_access_token' => $access_token])->get();
        if ($user->count() == 0) {
            http_response_code(401);
            echo json_encode([
                'status' => (string) 0,
                'message' => login_message(),
                'oData' => (object) array(),
                'errors' => (object) [],
            ]);
            exit;
        } else {
            $user = $user->first();
            if ($user->status == 'active') {
                return $user->id;
            } else {
                http_response_code(401);
                echo json_encode([
                    'status' => (string) 0,
                    'message' => login_message(),
                    'oData' => (object) array(),
                    'errors' => (object) [],
                ]);
                exit;
                return response()->json([
                    'status' => (string) 0,
                    'message' => login_message(),
                    'oData' => (object) array(),
                    'errors' => (object) [],
                ], 401);
                exit;
            }
        }
    }


    public function myOrders(Request $request)
    {
       
        $status = (string) 0;
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), [
           
        ]);

        if ($validator->fails()) {
            $status = (string) 0;
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $user_id = $this->validateAccesToken($request->access_token);
            $status = $request->status;
            $driver_lat = $request->driver_lat;
            $driver_long = $request->driver_long;

            $page = (int)$request->page??1;
            $limit= 10;
            $offset = ($page - 1) * $limit;
            
            
            $datamain = BookingPickUpOrder::with(['category_details', 'size_details', 'care_details'])
    ->where('pickup_driver', $user_id) // Ensure pickup_driver matches the user ID
    ->where('payment_status', 1);     // Filter by payment status

if (is_numeric($status)) {
    // Determine the alternate status based on your business logic
    $do_status = do_status($status);
   
    if ($status == 0) {
        $do_status = 5;
    }

    // Apply booking_status conditions based on driver equality
    $datamain = $datamain->where(function ($query) use ($status, $do_status) {
        $query->where(function ($q) use ($status, $do_status) {
            // Case 1: pickup_driver equals delivery_driver
            $q->whereColumn('pickup_driver', 'delivery_driver')
              ->where(function ($q2) use ($status, $do_status) {
                  $q2->where('booking_status', $status)
                     ->orWhere('booking_status', $do_status);
              });
        })
        ->orWhere(function ($q) use ($status) {
            // Case 2: pickup_driver does not equal delivery_driver
            $q->whereColumn('pickup_driver', '!=', 'delivery_driver')
              ->where('booking_status', $status);
        });
    });
}

// Apply pagination if 'page' parameter exists
if ($request->page) {
    $datamain = $datamain->skip($offset)->take($limit);
}

// Final ordering by ID in descending order
$datamain = $datamain->orderBy('id', 'desc')->get();
            foreach ($datamain as $key => $value) {

                
                $datadropoff = BookingDropOffOrder::where('pick_up_id',$value->id)->first();
                $datamain[$key]->booking_status_text = booking_status($value->booking_status);
                $datamain[$key]->type = order_type($value->booking_status);
                $datamain[$key]->distance = distanceCalculation($value->po_latitude, $value->po_longitude, $driver_lat, $driver_long);
                if($value->booking_status >= 5)
                {
                $datamain[$key]->distance = distanceCalculation($value->do_latitude, $value->do_longitude, $driver_lat, $driver_long);
                $datamain[$key]->order_number = $value->delivery_order_number;    
                }
                $datamain[$key]->do_location = $datadropoff->location??'';
                $datamain[$key]->do_landmark = $datadropoff->landmark??'';
                $datamain[$key]->do_contact_person = $datadropoff->contact_person??'';
                $datamain[$key]->do_dail_code = $datadropoff->dail_code??'';
                $datamain[$key]->do_mobile_no = $datadropoff->mobile_no??'';
                $tracking = Tracking::where('order_id',$value->id)->get();
                foreach ($tracking as $key_tr => $value_tr) {
                    $tracking[$key_tr]->status_text = booking_status($value_tr->status);
                    $tracking[$key_tr]->date = get_date_in_timezone($value_tr->created_at, config('global.datetime_format'));
                }
                $datamain[$key]->is_mute = MuteOrders::where(['order_id'=>$value->id,'driver_id'=>$user_id,'type'=>order_type($value->booking_status)])->get()->count();
                $datamain[$key]->traking = $tracking;
            }

           


            // $datamain2 = BookingPickUpOrder::with('category_details','size_details','care_details');
            // $datamain2 = $datamain2->where(function ($query) use ($user_id) {
            //     $query->where('delivery_driver', $user_id);
            // });

            // if(is_numeric($status))
            // { 
            //     if($status == 1)
            //     {   
            //         $datamain2 = $datamain2->where(function ($query) use ($status, $do_status) {
            //             $query->where('booking_status','>=', 4);
            //        });
            //     }

            //     if($status == 2)
            //     {   
            //         $datamain2 = $datamain2->where(function ($query) use ($status, $do_status) {
            //             $query->where('booking_status','=', 1);
            //        });
            //     }
            //     if($status == 3)
            //     {   
            //         $datamain2 = $datamain2->where(function ($query) use ($status, $do_status) {
            //             $query->where('booking_status','=', 1);
            //        });
            //     }
                
            
            // }
            // if($request->page)
            // {
            //     $datamain2 = $datamain2->skip($offset)->take($limit);
            // }
            
            // $datamain2 = $datamain2->where('payment_status',1)->orderBy('id','desc');



            $datamain2 = BookingPickUpOrder::with('category_details', 'size_details', 'care_details');

// Apply the condition for matching `delivery_driver` to `$user_id`
$datamain2 = $datamain2->where('delivery_driver', $user_id);

// Apply further conditions based on whether `delivery_driver` is equal to `pickup_driver`
$datamain2 = $datamain2->where(function ($query) use ($status, $do_status) {
    // Case when `delivery_driver` is the same as `pickup_driver`
    $query->whereColumn('delivery_driver', 'pickup_driver')
          ->when(is_numeric($status), function ($query) use ($status) {
              // Apply status conditions when both drivers are the same
              if ($status == 4) {
                  $query->where('booking_status', '>=', 4);
              } elseif ($status == 1) {
                  $query->where('booking_status', '=', 1);
              } elseif ($status == 0) {
                  $query->where('booking_status', '=', 1);
              }
          });
});

// Apply alternative conditions when `delivery_driver` is NOT the same as `pickup_driver`
$datamain2 = $datamain2->orWhere(function ($query) use ($user_id, $status, $do_status) {
    $query->where('delivery_driver', $user_id)
          ->whereColumn('delivery_driver', '!=', 'pickup_driver')
          ->when(is_numeric($status), function ($query) use ($status, $do_status) {
              // Apply alternative status conditions
              if ($status == 0) {
                  $query->where('booking_status', '=', 5);
              } elseif ($status == 1) {
                  $query->where('booking_status', '=', $do_status);
              } elseif ($status == 4) {
                  $query->where('booking_status', '=', $do_status);
              }
          });
});

// Apply pagination if `page` exists
if ($request->page) {
    $datamain2 = $datamain2->skip($offset)->take($limit);
}

// Final condition for payment status and order by ID
$datamain2 = $datamain2->where('payment_status', 1)->orderBy('id', 'desc');


            

          
            
            $datamain2 = $datamain2->get();
            
            foreach ($datamain2 as $key => $value) {
                $datadropoff = BookingDropOffOrder::where('pick_up_id',$value->id)->first();
                if($value->delivery_driver== $value->pickup_driver){
                $datamain2[$key]->booking_status_text = booking_status(4);
                $datamain2[$key]->booking_status = 4;
                }
                else{
                    $datamain2[$key]->order_number = $value->delivery_order_number;  
                    $datamain2[$key]->booking_status_text = booking_status($datamain2[$key]->booking_status);
                }
                $datamain2[$key]->type = "Pick Up";
                $datamain2[$key]->distance = distanceCalculation($value->po_latitude, $value->po_longitude, $driver_lat, $driver_long);
                $datamain2[$key]->do_location = $datadropoff->location??'';
                $tracking = Tracking::where('order_id',$value->id)->get();
                foreach ($tracking as $key_tr => $value_tr) {
                    $tracking[$key_tr]->status_text = booking_status($value_tr->status);
                    $tracking[$key_tr]->date = get_date_in_timezone($value_tr->created_at, config('global.datetime_format'));
                }
                $datamain2[$key]->is_mute = MuteOrders::where(['order_id'=>$value->id,'driver_id'=>$user_id,'type'=>'Pick Up'])->get()->count();
                $datamain2[$key]->traking = $tracking;
            }
            


            $datamain = array_merge($datamain->toArray(), $datamain2->toArray());


            $sortArr = array_column($datamain, 'id');
            array_multisort($sortArr, SORT_DESC, $datamain);

        $limit = 10;
        $page_no = 1;
        if (isset($request->page) && $request->page != "") {
            $page_no = $request->page;
        }

        $start_from = ($page_no - 1) * $limit;

        $total = count($datamain);
        $totalPages = ceil($total / $limit);
        $page_no = max($page_no, 1);
        $page_no = min($page_no, $totalPages);
        $offset = ($page_no - 1) * $limit;
        if ($offset < 0) {
            $offset = 0;
        }

        $datamain = array_slice($datamain, $offset, $limit);



            
            $o_data = convert_all_elements_to_string($datamain);

            foreach ($o_data as $key => $value) {
                if(empty($value['care_details']))
                {
                    $o_data[$key]['care_details'] = (object) [];
                }
                
            }
        }
        $status = "1";
        $message = "My orders";
        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => $o_data]);
    }

    public function myOrderDetails(Request $request)
    {

        $status = (string) 0;
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), [
           
        ]);
        
        if ($validator->fails()) {
            $status = (string) 0;
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $user_id = $this->validateAccesToken($request->access_token);
            $driver_lat = $request->driver_lat;
            $driver_long = $request->driver_long;


            $datadropoff = BookingDropOffOrder::where('pick_up_id',$request->order_id)->first();
           
            $datamain = BookingPickUpOrder::with('category_details','size_details','care_details');
            $datamain = $datamain->where(function ($query) use ($user_id) {
                $query->where('pickup_driver', $user_id)
                      ->orWhere('delivery_driver', $user_id);
            });
            $datamain = $datamain->where('payment_status',1)->where('id',$request->order_id)->get()->first();
            $datamain->booking_status_text = booking_status($datamain->booking_status);
            $datamain->type = order_type($datamain->booking_status);
            $datamain->distance = distanceCalculation($datamain->po_latitude, $datamain->po_longitude, $driver_lat, $driver_long);
            if($request->status >= 5)
            {
            $datamain->distance = distanceCalculation($datamain->do_latitude, $datamain->do_longitude, $driver_lat, $driver_long);
            $datamain->order_number = $datamain->delivery_order_number;    
            }
            $datamain->payment_type_text = payment_type($datamain->payment_type);
            $datamain->do_location = $datadropoff->location??'';
            $datamain->do_landmark = $datadropoff->landmark??'';
            $datamain->do_contact_person = $datadropoff->contact_person??'';
            $datamain->do_dail_code = $datadropoff->dail_code??'';
            $datamain->do_mobile_no = $datadropoff->mobile_no??'';
            $datamain->images = RequestImages::where('request_id',$request->order_id)->get();
            $tracking = Tracking::where('order_id',$request->order_id)->get();
                foreach ($tracking as $key_tr => $value_tr) {
                    $tracking[$key_tr]->status_text = booking_status($value_tr->status);
                    $tracking[$key_tr]->date = get_date_in_timezone($value_tr->created_at, config('global.datetime_format'));
                }
            $datamain->traking = $tracking;

            $customer = User::select('id','name','dial_code','phone','profile_image')->find($datamain->customer_id);
            if(!empty($customer))
            {
                $img = empty($customer->profile_image) ? asset("storage/placeholder.png") : asset("storage/user/".$customer->profile_image);
                $customer->profile_image = (string) $img;
                $datamain->customer_details = $customer;
            }
            else
            {
                $datamain->customer_details = (object) [];
            }
            
 
            
            $o_data = convert_all_elements_to_string($datamain->toArray());
            if(empty($o_data['care_details']))
            {  
                $o_data['care_details'] = (object) [];
            }
            if(!empty($datamain->images) && count($datamain->images) == 0)
            {
                $o_data['images'] = [];  
            }
        }
        $status = "1";
        $message = "My order deatils";
        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => $o_data]);
    }

    public function changeStatus(Request $request)
    {

        $status = (string) 0;
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status' => 'required',
        ]);
        
        if ($validator->fails()) {
            $status = (string) 0;
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $user_id = $this->validateAccesToken($request->access_token);
            $status = $request->status;
            $order_id = $request->order_id;

             //save to tracking history

             $datamain = BookingPickUpOrder::where('id',$order_id)->first();
          
             if($datamain->booking_status >= 5)
             {
                if($request->status == 1)
                {
                    $status = 6;
                }
                if($request->status == 4)
                {
                    $status = 8;
                }
                
             }

             if ($file = $request->file("signature")) {
                $response = image_upload($request, 'user', 'signature');
                if ($response['status']) {
                    $signature = $response['link'];
                }
            }
           
             
             $check = Tracking::where('order_id',$order_id)->where('status',$status)->get()->count();
             if($check == 0)
             {
                $datadropoff = BookingPickUpOrder::where('id',$order_id)->first();
                $datadropoff->booking_status = $status;
                $datadropoff->signature = $signature??'';
                $datadropoff->comment = $request->comment??'';
                $datadropoff->save();

                 $datatrack = new Tracking;
                 $datatrack->order_id = $order_id;
                 $datatrack->status = $status;
                 $datatrack->created_at = gmdate('Y-m-d H:i:s');
                 $datatrack->save();
             }

             $user = User::find($datamain->customer_id);
             $order_no = $datamain->order_number;
             $driver_id = $datamain->pickup_driver??0;
             if($status > 4)
             {
             $order_no = $datamain->delivery_order_number;
             $driver_id = $datamain->delivery_driver??0; 
             }
             $title = $order_no;

             $description = "Your order status changed to ".booking_status($status);
             $notification_id = time();
             $ntype = 'status_changed';
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
                 Artisan::call('send:send_booking_status_email', ['booking_id' => $datamain->id]);
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


             $user = User::find($driver_id);
             $order_no = $datamain->order_number;
             if($status > 4)
             {
             $order_no = $datamain->delivery_order_number;
             }
             $title = $order_no;

             $description = "Order status changed to ".booking_status($status)." successfully";
             $notification_id = time();
             $ntype = 'status_changed_driver';
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
             
             
             $odata['order_id'] = (string) $order_id;
             $odata['status'] = (string) $status;
            
        }
        $status = "1";
        $message = "Status changed successfully";
        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors,'oData' => $odata]);
    }
    

    public function muteOrder(Request $request)
    {
       
        $status = (string) 0;
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), [
           
        ]);

        if ($validator->fails()) {
            $status = (string) 0;
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $user_id = $this->validateAccesToken($request->access_token);
            $status = $request->status;
            $driver_lat = $request->driver_lat;
            $driver_long = $request->driver_long;

            $page = (int)$request->page??1;
            $limit= 10;
            $offset = ($page - 1) * $limit;
            
            
            $datamain = BookingPickUpOrder::with('category_details','size_details','care_details');
            $datamain = $datamain->where(function ($query) use ($user_id) {
                $query->where('pickup_driver', $user_id);
                    //   ->orWhere('delivery_driver', $user_id);
            });

            if(is_numeric($status))
            {
                $do_status = do_status($status);
                if($status == 0)
                {
                    $do_status = 5;
                }
                $datamain = $datamain->where(function ($query) use ($status, $do_status) {
                $query->where('booking_status', $status)
                      ->orWhere('booking_status', $do_status);
                   
            });
            }

             if($request->order_id)
                {
                    $datamain = $datamain->where('id',$request->order_id);
                }
                
           
            
            $datamain = $datamain->where('payment_status',1)->orderBy('id','desc')->get();
            foreach ($datamain as $key => $value) {

                
                $datadropoff = BookingDropOffOrder::where('pick_up_id',$value->id)->first();
                $datamain[$key]->booking_status_text = booking_status($value->booking_status);
                $datamain[$key]->type = order_type($value->booking_status);
                $datamain[$key]->distance = distanceCalculation($value->po_latitude, $value->po_longitude, $driver_lat, $driver_long);
                if($value->booking_status >= 5)
                {
                $datamain[$key]->distance = distanceCalculation($value->do_latitude, $value->do_longitude, $driver_lat, $driver_long);
                $datamain[$key]->order_number = $value->delivery_order_number;    
                }
                $datamain[$key]->do_location = $datadropoff->location??'';
                $datamain[$key]->do_landmark = $datadropoff->landmark??'';
                $datamain[$key]->do_contact_person = $datadropoff->contact_person??'';
                $datamain[$key]->do_dail_code = $datadropoff->dail_code??'';
                $datamain[$key]->do_mobile_no = $datadropoff->mobile_no??'';
                $tracking = Tracking::where('order_id',$value->id)->get();
                foreach ($tracking as $key_tr => $value_tr) {
                    $tracking[$key_tr]->status_text = booking_status($value_tr->status);
                    $tracking[$key_tr]->date = get_date_in_timezone($value_tr->created_at, config('global.datetime_format'));
                }
                $datamain[$key]->traking = $tracking;
            }

           


            $datamain2 = BookingPickUpOrder::with('category_details','size_details','care_details');
            $datamain2 = $datamain2->where(function ($query) use ($user_id) {
                $query->where('delivery_driver', $user_id);
            });

            if(is_numeric($status))
            { 
                if($status == 4)
                {   
                    $datamain2 = $datamain2->where(function ($query) use ($status, $do_status) {
                        $query->where('booking_status','>=', 4);
                   });
                }

                if($status == 1)
                {   
                    $datamain2 = $datamain2->where(function ($query) use ($status, $do_status) {
                        $query->where('booking_status','=', 1);
                   });
                }
                if($status == 0)
                {   
                    $datamain2 = $datamain2->where(function ($query) use ($status, $do_status) {
                        $query->where('booking_status','=', 1);
                   });
                }
                
            
            }

            if($request->order_id)
            {
               $datamain2 = $datamain2->where('id',$request->order_id);
            }
             
            $datamain2 = $datamain2->where('payment_status',1)->orderBy('id','desc');
          
            
            $datamain2 = $datamain2->get();
            foreach ($datamain2 as $key => $value) {
                $datadropoff = BookingDropOffOrder::where('pick_up_id',$value->id)->first();
                $datamain2[$key]->booking_status_text = booking_status(4);
                $datamain2[$key]->booking_status = 4;
                $datamain2[$key]->type = "Pick Up";
                $datamain2[$key]->distance = distanceCalculation($value->po_latitude, $value->po_longitude, $driver_lat, $driver_long);
                $datamain2[$key]->do_location = $datadropoff->location??'';
                $tracking = Tracking::where('order_id',$value->id)->get();
                foreach ($tracking as $key_tr => $value_tr) {
                    $tracking[$key_tr]->status_text = booking_status($value_tr->status);
                    $tracking[$key_tr]->date = get_date_in_timezone($value_tr->created_at, config('global.datetime_format'));
                }
                $datamain2[$key]->traking = $tracking;
            }
            


            $datamain = array_merge($datamain->toArray(), $datamain2->toArray());


            $sortArr = array_column($datamain, 'id');
            array_multisort($sortArr, SORT_DESC, $datamain);

        $limit = 10;
        $page_no = 1;
        if (isset($request->page) && $request->page != "") {
            $page_no = $request->page;
        }

        $start_from = ($page_no - 1) * $limit;

        $total = count($datamain);
        $totalPages = ceil($total / $limit);
        $page_no = max($page_no, 1);
        $page_no = min($page_no, $totalPages);
        $offset = ($page_no - 1) * $limit;
        if ($offset < 0) {
            $offset = 0;
        }

        $datamain = array_slice($datamain, $offset, $limit);
      
        foreach ($datamain as $key => $value) {
            $type = $value['type'];
             if($request->type == 'Drop Off')
             {
                 $type = "Drop Off";
             }
             if($request->type == 'Pick Up')
             {
                 $type = "Pick Up";
             }
            $check = MuteOrders::where(['order_id'=>$value['id'],'driver_id'=>$user_id,'type'=>$type])->get()->count();
            if($check == 0)
            {
                $datains = new MuteOrders;
                $datains->order_id = $value['id'];
                $datains->driver_id = $user_id;
                $datains->type = $type;
                $datains->save();
            }
            
        } 
            
            $o_data = [];

            foreach ($o_data as $key => $value) {
                if(empty($value['care_details']))
                {
                    $o_data[$key]['care_details'] = (object) [];
                }
                
            }
        }
        $status = "1";
        $message = "Mute";
        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => $o_data]);
    }
}