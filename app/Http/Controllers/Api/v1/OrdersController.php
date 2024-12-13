<?php

namespace App\Http\Controllers\Api\v1;

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


    public function create_order(Request $request)
    {

        $status = (string) 0;
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'pu_location' => 'required',
            'do_location' => 'required',
            'pu_contact_person' => 'required',
            'do_contact_person' => 'required',
            'size_id' => 'required',
            'delivery_type' => 'required',
        ]);

        if ($validator->fails()) {
            $status = (string) 0;
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $user_id = $this->validateAccesToken($request->access_token);
            $user = User::find($user_id);
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
            'customer_id' => $user_id,
            'category_id' => $request->category_id,
            'location' => $request->pu_location,
            'po_latitude' => $request->po_latitude,
            'po_longitude' => $request->po_longitude,
            'do_latitude' => $request->do_latitude,
            'do_longitude' => $request->do_longitude,
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
            'building_number_po'   => $request->building_number_po??0,
            'building_number_do'   => $request->building_number_do??0,
            'payment_status' => 0,
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
            $pick_up_insert->building_number_po =$request->building_number_po;
            $pick_up_insert->building_number_do =$request->building_number_do;
            $pick_up_insert->order_number = "P-".date('Ym').$last_pick_up_id;
            $pick_up_insert->delivery_order_number = "D-".date('Ym').$last_pick_up_id;
            $pick_up_insert->save();
        }
        

        

        $do_insert = 
        [
            'order_number'=> '',
            'pick_up_id' => $last_pick_up_id,
            'customer_id' => $user_id,
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


        }

        if(!empty($request->payment_type))
        {
           if($request->payment_type != 4)
           {
             $payment_response = $this->WalletStripePayment($request->grand_total_input, $user);
             
             $o_data['payment_ref'] = $payment_response->client_secret;
           }
           else
           {
            $o_data['payment_ref'] = rand(1000000, 9999999)."_".rand(1000000, 9999999);
           }

        
           BookingPickUpOrder::where('id',$last_pick_up_id)->update(['payment_ref'=>$o_data['payment_ref']]);
        }

        $status = "1";
        $message = "Order created successfully!";
        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => $o_data]);
    }

    public function priceDetails(Request $request)
    {

        $status = (string) 0;
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'size_id' => 'required',
            'delivery_type' => 'required',
        ]);

        if ($validator->fails()) {
            $status = (string) 0;
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
           // $user_id = $this->validateAccesToken($request->access_token);

            $request->category_id;
            $request->size_id;
            $costing = Costing::select("cost")->where('category_id', $request->category_id)
            ->where('size_id', $request->size_id)->where('delivery_type',ucfirst( $request->delivery_type))->first();
            $o_data['cost'] = number_format($costing->cost??0, 2, '.', '');
            $o_data['service_charge'] = number_format(0, 2, '.', '');
            $o_data['tax'] = number_format(0, 2, '.', '');
            $o_data['grand_total'] = number_format(0, 2, '.', '');

            if(!empty($costing->cost))
            {
                $cost = $costing->cost;
                $settings = Settings::find(1);
                $service_charge = $settings->service_charge;
                $tax_percentage = $settings->tax_percentage;

                if(!empty($tax_percentage))
                {
                    $tax = (($cost * $tax_percentage)/100);
                }
                
                
                $o_data['service_charge'] = number_format($service_charge, 2, '.', '');
                $o_data['tax'] = number_format($tax, 2, '.', '');
                $o_data['grand_total'] = number_format($cost + $tax + $service_charge, 2, '.', '');
            }
            
        }
        $status = "1";
        $o_data = convert_all_elements_to_string($o_data);
        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => $o_data]);
    }
    public function WalletStripePayment( $balance, $user )
    {
        try {
            
           // $balance = CurrencyConverter2("AED", 'AED', $balance);
            
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $checkout_session = \Stripe\PaymentIntent::create([
                'amount' => ceil($balance) * 100,
                'currency' => 'AED',
                'description' => 'Wallet Recharge payment',
                'shipping' => [
                    'name' => $user->name,
                    'address' => [
                        'line1' => 'Dubai',
                        'postal_code' => 12345,
                        'city' => 'Dubai',
                        'state' => 'Dubai',
                        'country' => 'United Arab Emirates',
                    ],
                ]
            ]);
            return($checkout_session);
        } catch (\Exception $e) {
            return("fail");
        }
    }
    public function paymentSuccess(Request $request)
    {
        $status = (string) 0;
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), [
            'payment_ref' => 'required',
        ]);

        if ($validator->fails()) {
            $status = (string) 0;
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $user_id = $this->validateAccesToken($request->access_token);
            $user = User::find($user_id);
            $image_path = "";
      
            $datamain = BookingPickUpOrder::where('payment_ref',$request->payment_ref)->first();
            $datamain->payment_status = 1;
            $datamain->save();
            Artisan::call('send:send_booking_email', ['booking_id' => $datamain->id]);
            $o_data['order_id'] = (string) $datamain->id;
            $o_data['order_number'] = $datamain->order_number;

            //save to tracking history
            $check = Tracking::where('order_id',$datamain->id)->where('status',0)->get()->count();
            if($check == 0)
            {
                $datatrack = new Tracking;
                $datatrack->order_id = $datamain->id;
                $datatrack->status = 0;
                $datatrack->created_at = gmdate('Y-m-d H:i:s');
                $datatrack->save();
            }
            


                $order_no = $datamain->order_number;
                $title = $order_no ;//'Order Placed Successfully';
                $description = "Your order placed successfully.For more information, Please check the Order Status.";
                $notification_id = time();
                $ntype = 'order_placed';
                if (!empty($user->firebase_user_key)) {
                    $notification_data["Notifications/" . $user->firebase_user_key . "/" . $notification_id] = [
                        "title" => $title,
                        "description" => $description,
                        "notificationType" => $ntype,
                        "createdAt" => gmdate("d-m-Y H:i:s", $notification_id),
                        "orderId" => (string) $datamain->id,
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
                            "orderId" => (string) $datamain->id,
                            "imageURL" => "",
                        ]
                    );
                    
                 // print_r($res);
                }

                
        }

        $status = "1";
        $message = "Order created successfully!";
        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => $o_data]);
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

            $page = (int)$request->page??1;
            $limit= 10;
            $offset = ($page - 1) * $limit;
            
            $datamain = BookingPickUpOrder::with('category_details','size_details','care_details')->where('customer_id',$user_id)->where('payment_status',1)->skip($offset)->take($limit)->orderBy('id','desc')->get();
            foreach ($datamain as $key => $value) {
                $datadropoff = BookingDropOffOrder::where('pick_up_id',$value->id)->first();
                $datamain[$key]->booking_status_text = booking_status($value->booking_status);
                $datamain[$key]->do_location = $datadropoff->location??'';
                $datamain[$key]->type = order_type($value->booking_status);
                $tracking = Tracking::where('order_id',$value->id)->get();
                foreach ($tracking as $key_tr => $value_tr) {
                    $tracking[$key_tr]->status_text = booking_status($value_tr->status);
                    $tracking[$key_tr]->date = get_date_in_timezone($value_tr->created_at, config('global.datetime_format'));
                }
                $datamain[$key]->traking = $tracking;
            }
            $o_data = convert_all_elements_to_string($datamain);
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

            $datadropoff = BookingDropOffOrder::where('pick_up_id',$request->order_id)->first();
           
            $datamain = BookingPickUpOrder::with('category_details','size_details','care_details','pickeupdriver','deliverydriver')->where('customer_id',$user_id)->where('payment_status',1)->where('id',$request->order_id)->get()->first();
            $datamain->booking_status_text = booking_status($datamain->booking_status);
            $datamain->payment_type_text = payment_type($datamain->payment_type);
            if($datamain->booking_status >= 5)
            {
                $datamain->order_number = $datamain->delivery_order_number; 
            }
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
            $datamain->type = order_type($datamain->booking_status);
           
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
}