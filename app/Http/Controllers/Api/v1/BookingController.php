<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponFor;
use App\Models\MutedUserBooking;
use App\Models\OrderModel;
use App\Models\User;
use App\Models\UserBooking;
use App\Models\UserBookingImage;
use App\Models\UserBookingStatus;
use App\Models\UserRating;
use App\Models\UserVehicle;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use App\Services\UserFBNotificationService;

class BookingController extends Controller
{

    protected $userFBNotifications;

    public function __construct(UserFBNotificationService $userFBNotifications)
    {

        $this->userFBNotifications = $userFBNotifications;
    }

    public function placeBooking(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'drop_off_location' => 'required',
                'drop_off_lat' => 'required',
                'drop_off_lng' => 'required',
                'pick_up_location' => 'required',
                'pick_up_lat' => 'required',
                'pick_up_lng' => 'required',
                'vehicle_id' => 'required',
                'emergency_type_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $booking_number = $this->generateOrderNumber();

            $vehicle = UserVehicle::find($request->vehicle_id);

            $booking = UserBooking::create([
                'drop_off_location' => $request->drop_off_location,
                'drop_off_lat' => $request->drop_off_lat,
                'drop_off_lng' => $request->drop_off_lng,
                'pick_up_location' => $request->pick_up_location,
                'pick_up_lat' => $request->pick_up_lat,
                'pick_up_lng' => $request->pick_up_lng,
                'vehicle_id' => $request->vehicle_id,
                'user_id' => $user->id,
                'emergency_type_id' => $request->emergency_type_id,
                'remarks' => $request->remarks,
                'booking_status' => 0,
                'booking_number' => $booking_number,
                'vehicle_type_id' => $vehicle->category_id
            ]);

            if ($booking) {
                UserBookingStatus::create([
                    'status' => 0,
                    'user_id' => $user->id,
                    'booking_id' => $booking->id
                ]);

                if ($request->has('images')) {
                    $files = $request->images;
                    foreach ($files as $file) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $s3Path = 'imove/bookings/' . $fileName;
                        $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
                        UserBookingImage::create([
                            'images_path' => \Storage::disk('s3')->url($s3Path),
                            'booking_id' => $booking->id
                        ]);
                    }
                }

                $booking = UserBooking::find($booking->id);
                $oData['booking'] = convertNumbersToStrings($booking->toArray());

                // This is for customer notification
                $users[] = $user;
                $this->userFBNotifications->addUserNotification($users, [
                    'title' => 'Booking Placed',
                    'message' => 'New booking has placed against booking number: ' . $booking->booking_number,
                    'imageURL' => '',
                    'notificationType' => 'booking_placed',
                    'status' => '0',
                    'booking_status' => booking_status(0),
                    'booking_number' => $booking->booking_number
                ], true, true);

                // This is for driver notifications
                $vehicle_type = VehicleType::find($vehicle->category_id);
                $drivers = User::where('vehicle_type', 'like', '%' . $vehicle_type->model . '%')
                    ->where('role_id', 2)
                    ->whereNotNull('user_device_token')
                    ->whereNotNull('firebase_user_key')
                    ->get();

                $this->userFBNotifications->addUserNotification($drivers, [
                    'title' => 'Booking Placed',
                    'message' => 'New booking has placed against booking number: ' . $booking->booking_number,
                    'imageURL' => '',
                    'notificationType' => 'booking_received',
                    'status' => '0',
                    'booking_status' => booking_status(0),
                    'booking_number' => $booking->booking_number
                ], true);

                return return_response('1', 200, 'Order has placed', [], $oData);
            } else {
                return return_response('0', 500, 'Order has not been placed');
            }
        } catch (\Exception $exception) {
            dd($exception);
            Log::info($exception);
            return return_response('0', 500, 'Something went wrong');
        }
    }

    private function generateOrderNumber()
    {
        $latestOrder = UserBooking::orderBy('id', 'desc')->whereNotNull('booking_number')->first();

        if (!$latestOrder) {
            $nextOrderNumber = 'IM-0000001';
        } else {
            $lastOrderNumber = $latestOrder->booking_number;
            $numericPart = (int)substr($lastOrderNumber, 3);
            $newNumber = $numericPart + 1;
            $nextOrderNumber = 'IM-' . str_pad($newNumber, 7, '0', STR_PAD_LEFT);
        }

        return $nextOrderNumber;
    }

    public function getMyBookings(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $limit = $request->has('limit') ? $request->limit : 10;
            $page = $request->has('page') ? $request->page : 1;
            $offset = ($page - 1) * $limit;

            $myBookings = UserBooking::where(['user_id' => $user->id])
                ->limit($limit)
                ->offset($offset)
                ->latest()->get()->toArray();

            $oData['my_bookings'] = convertNumbersToStrings($myBookings);
            return return_response('1', 200, 'Bookings fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function bookingDetails(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_number' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $myBooking = UserBooking::where(['booking_number' => $request->booking_number])
                ->when(!$request->is_vendor, function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })
                ->first();

            if ($request->has('is_vendor') && $request->is_vendor) {
                $order = OrderModel::where(['booking_id' => $myBooking->id, 'vendor_id' => $user->id])->first();

                if ($order) {
                    $amount = $order->amount;
                    $commission = config('global.commission');
                    $commission_amount = ($amount * $commission) / 100;
                    $commission_amount = number_format($commission_amount, 2);
                    $total_amount = $amount - $commission_amount;

                    $oData['order'] = convertNumbersToStrings($order->toArray());
                    $oData['amount'] = number_format($amount, 2);
                    $oData['commission'] = number_format($commission_amount, 2);
                    $oData['grand_total'] = number_format($total_amount, 2);
                }
            }

            $oData['my_booking'] = $myBooking ? convertNumbersToStrings($myBooking->toArray()) : (object)[];

            return return_response('1', 200, 'Booking fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function placeBid(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required',
                'amount' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            if ($user->role_id != '2') {
                return return_response('0', 200, 'Something went wrong');
            }

            $exists_bid = OrderModel::where([
                'booking_id' => $request->booking_id,
                'vendor_id' => $user->id,
                'status' => 'pending'
            ])->exists();

            $booking = UserBooking::find($request->booking_id);

            if ($exists_bid) {
                return return_response('0', 200, 'You already bid this booking');
            }

            $order = OrderModel::create([
                'booking_id' => $request->booking_id,
                'vendor_id' => $user->id,
                'status' => 'pending',
                'amount' => $request->amount
            ]);

            $booking = UserBooking::find($request->booking_id);
            $booking->save();
            $oData['is_quet_request_sent'] = '1';
            $oData['order'] = convertNumbersToStrings($order->toArray());
            $oData['vendor'] = convertNumbersToStrings($user->toArray());
            $oData['booking'] = convertNumbersToStrings($booking->toArray());

            // Driver notification
            $users[] = $user;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Bid Placed',
                'message' => 'A bid has been placed against booking number # ' . $booking->booking_number,
                'imageURL' => '',
                'notificationType' => 'booking_bid_placed',
                'status' => '1',
                'booking_status' => booking_status(1),
                'booking_number' => $booking->booking_number
            ], true, true);

            // Customer Notification
            $users[] = $booking->customer;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Bid Placed',
                'message' => 'A bid has been placed against booking number # ' . $booking->booking_number,
                'imageURL' => '',
                'notificationType' => 'booking_bid_received',
                'status' => '1',
                'booking_status' => booking_status(1),
                'booking_number' => $booking->booking_number
            ], true);

            return return_response('1', 200, 'Bid Placed', [], $oData);
        } catch (\Exception $exception) {
            Log::info($exception);
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'order_id' => 'required',
                'otp' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            if ($user->role_id != '2') {
                return return_response('0', 200, 'Something went wrong');
            }

//            $order_status = 'pending';
//            $booking_status = 1;
//            if ($request->deliver) {
            $order_status = 'completed';
            $booking_status = 5;
//            }

            $order = OrderModel::where('id', $request->order_id)->where('otp', $request->otp)->first();
            if (empty($order)) {
                return return_response('0', 200, 'Please check OTP');
            }
            $order->otp_verified = 1;
            $order->otp = null;
            $order->status = $order_status;
            $order->save();

            UserBooking::where('id', $order->booking_id)->update(['booking_status' => $booking_status, 'driver_remarks' => $request->remarks]);

            UserBookingStatus::create([
                'status' => $booking_status,
                'user_id' => $user->id,
                'booking_id' => $order->booking_id
            ]);

            $booking = UserBooking::find($order->booking_id);
            if (!$request->deliver) {
                $oData['is_quet_request_sent'] = '1';
            }
            $oData['order'] = convertNumbersToStrings($order->toArray());
            $oData['vendor'] = convertNumbersToStrings($user->toArray());
            $oData['booking'] = convertNumbersToStrings($booking->toArray());

            // Driver notification
            $users[] = $order->vendor;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Order completed',
                'message' => 'Order has been completed against booking number# ' . $booking->booking_number,
                'imageURL' => '',
                'notificationType' => 'booking_status_change',
                'status' => '5',
                'booking_status' => booking_status(5),
                'booking_number' => $booking->booking_number
            ], true, true);

            // Customer Notification
            $users[] = $booking->customer;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Order completed',
                'message' => 'Order has been completed against booking number# ' . $booking->booking_number,
                'imageURL' => '',
                'notificationType' => 'booking_status_change',
                'status' => '5',
                'booking_status' => booking_status(5),
                'booking_number' => $booking->booking_number
            ], true);

            return return_response('1', 200, 'Otp Verified', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function startWork(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required',
                'order_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $order = OrderModel::where('id', $request->order_id)->where('status', 'approved')->first();
            if (!$order) {
                return return_response('0', 200, 'No order found');
            }
            $order->status = 'on_going';
            $order->save();

            UserBookingStatus::create([
                'status' => 3,
                'user_id' => $user->id,
                'booking_id' => $request->booking_id
            ]);

            $booking = UserBooking::find($request->booking_id);
            $booking->booking_status = 3;
            $booking->save();

            $oData['booking'] = convertNumbersToStrings($booking->toArray());
            $oData['order'] = convertNumbersToStrings($order->toArray());

            // Driver notification
            $users[] = $order->vendor;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Work Started',
                'message' => 'Order has been started booking number # ' . $booking->booking_number,
                'imageURL' => '',
                'notificationType' => 'booking_status_change',
                'status' => '3',
                'booking_status' => booking_status(3),
                'booking_number' => $booking->booking_number
            ], true, true);

            // Customer Notification
            $users[] = $booking->customer;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Work Started',
                'message' => 'Order has been started booking number # ' . $booking->booking_number,
                'imageURL' => '',
                'notificationType' => 'booking_status_change',
                'status' => '3',
                'booking_status' => booking_status(3),
                'booking_number' => $booking->booking_number
            ], true);

            return return_response('1', 200, 'Work started', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getAllOrders(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $booking = UserBooking::find($request->booking_id);
            $vehicle_type = VehicleType::find($booking->vehicle->category_id);
            $available_vendors = User::where(['deleted' => 0, 'status' => 'active'])
                ->where('vehicle_type', 'like', '%' . $vehicle_type->model . '%')
                ->get();
            $available_vendors = $available_vendors->pluck('id')->toArray();

            $orders = OrderModel::where(['booking_id' => $request->booking_id, 'status' => 'pending'])
                ->whereIn('vendor_id', $available_vendors)
                ->get();
            $oData['orders'] = convertNumbersToStrings($orders->toArray());

            return return_response('1', 200, 'Orders fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function acceptOrder(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required',
                'order_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $order = OrderModel::find($request->order_id);
            if (empty($order)) {
                return return_response('0', 200, 'No order found');
            }

            UserBooking::where('id', $request->booking_id)
                ->update(['order_id' => $order->id, 'booking_status' => 2, 'dial_code' => $order->vendor->dial_code, 'mobile_number' => $order->vendor->phone]);
            $order->status = 'approved';
            $order->save();
            $booking = UserBooking::find($request->booking_id);

            $amount = $order->amount;
            $tax = 5;
            $taxed_amount = ($amount * $tax) / 100;
            $grand_total = $taxed_amount + $amount;
            $order = OrderModel::find($request->order_id);

            $oData['order'] = convertNumbersToStrings($order->toArray());
            $oData['sub_total'] = (string)$amount;
            $oData['tax'] = (string)$tax;
            $oData['grand_total'] = (string)$grand_total;
            $oData['booking'] = convertNumbersToStrings($booking->toArray());

            // Driver notification
            $users[] = $order->vendor;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Bid Accepted',
                'message' => 'A order has been confirmed against booking number# ' . $booking->booking_number,
                'imageURL' => '',
                'notificationType' => 'booking_bid_accepted',
                'status' => '2',
                'booking_status' => booking_status(2),
                'booking_number' => $booking->booking_number
            ], true, true);

            // Customer Notification
            $users[] = $booking->customer;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Bid Accepted',
                'message' => 'A order has been confirmed against booking number# ' . $booking->booking_number,
                'imageURL' => '',
                'notificationType' => 'booking_bid_accepted',
                'status' => '2',
                'booking_status' => booking_status(2),
                'booking_number' => $booking->booking_number
            ], true);

            return return_response('1', 200, 'Order accepted', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function createStripePayment(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'order_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $order = OrderModel::find($request->order_id);
            if (empty($order)) {
                return return_response('0', 200, 'No order found');
            }

            $amount = $order->amount;
            $tax = 5;
            $taxed_amount = ($amount * $tax) / 100;
            $grand_total = $taxed_amount + $amount;

            $paymentIntent = PaymentIntent::create([
                'amount' => $grand_total * 100,
                'currency' => 'USD',
                'payment_method_types' => ['card'],
            ]);

            $oData['payment_ref'] = $paymentIntent->client_secret;

            return return_response('1', 200, 'Payment token received', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function cancelOrder(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'order_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $order = OrderModel::find($request->order_id);
            $booking = UserBooking::find($order->booking_id);
            $order->delete();

            return return_response('1', 200, 'Order has been cancelled');
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function addRating(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required',
                'rating' => 'required|numeric|min:0|max:5'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $exists_review = UserRating::where([
                'user_id' => $user->id,
                'vendor_id' => $request->vendor_id,
                'booking_id' => $request->booking_id
            ])->exists();

//            dd($exists_review);
            if ($exists_review) {
                return return_response('0', 200, 'You already rated this booking');
            }

            UserRating::create([
                'rating' => $request->rating,
                'review' => $request->review,
                'user_id' => $user->id,
                'vendor_id' => $request->vendor_id,
                'booking_id' => $request->booking_id
            ]);

            $ratings = UserRating::where('vendor_id', $request->vendor_id);
            $total_ratings = $ratings->sum('rating');
            $total_count = $ratings->count();

            $average_rating = $total_ratings / $total_count;
            if ($average_rating > 5) {
                $average_rating = 5;
            }

            $booking = UserBooking::where('id', $request->booking_id)->first();
            $vendor = User::find($request->vendor_id);
            $vendor->ratings = $average_rating;
            $vendor->save();

            $oData['booking'] = convertNumbersToStrings($booking->toArray());
            $oData['vendor'] = convertNumbersToStrings($vendor->toArray());

            return return_response('1', 200, 'Rating saved', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function confirmPayment(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $booking = UserBooking::where('id', $request->booking_id)->update([
                'payment_reference' => $request->payment_reference,
                'payment_confirmed' => 1,
                'booking_status' => 2
            ]);

            UserBookingStatus::create([
                'status' => 2,
                'user_id' => $user->id,
                'booking_id' => $request->booking_id
            ]);

            $booking = UserBooking::find($request->booking_id);
            $oData['booking'] = convertNumbersToStrings($booking->toArray());

            return return_response('1', 200, 'Payment confirmed', [], $oData);
        } catch (\Exception $exception) {
            Log::info($exception);
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getAllAvailableBookings(Request $request)
    {
        try {
            $user = User::where('user_access_token', $request->access_token)->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $vehicle_types = $user->vehicle_type;
            $type_ids = [];
            if (!empty($vehicle_types)) {
                $vehicle_types = explode(',', $user->vehicle_type);
                $vehicle_types = VehicleType::whereIn('model', $vehicle_types)->get();
                $type_ids = $vehicle_types->pluck('id')->toArray();
            }

            $order_ids = [];
            if ($request->status !== null) {
                $status[] = $request->status;
            } else {
                $status[0] = 0;
            }

            if (!in_array($request->status, [0, 1]) || !$request->status) {
                $orders = OrderModel::where(['vendor_id' => $user->id])->get();
                $order_ids = $orders->pluck('id')->toArray();
            }
//
//            dd($user->id, $order_ids);

            $limit = $request->has('limit') ? $request->limit : 10;
            $page = $request->has('page') ? $request->page : 1;
            $offset = ($page - 1) * $limit;

            $bookings = UserBooking::latest()
                ->when(!empty($type_ids), function ($q) use ($type_ids) {
                    return $q->whereIn('vehicle_type_id', $type_ids);
                })
                ->when($request->status != null, function ($q) use ($request, $order_ids, $status) {
                    return $q->whereIn('booking_status', $status)
                        ->when(!in_array($request->status, [0]), function ($q1) use ($order_ids) {
                            return $q1->whereIn('order_id', $order_ids);
                        });
                })
                ->when($request->status === null, function ($q) use ($request, $order_ids, $status) {
                    return $q->whereIn('booking_status', $status)
                        ->when(count($order_ids), function ($q1) use ($order_ids) {
                            return $q1->orWhereIn('order_id', $order_ids);
                        });
                });

            if ($request->without_pagination) {
                $bookings = $bookings->get();
            } else {
                $bookings = $bookings->limit($limit)
                    ->offset($offset)
                    ->get();
            }

            $oData['bookings'] = convertNumbersToStrings($bookings->toArray());

            return return_response('1', 200, 'Bookings fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function deliver(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'order_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where('user_access_token', $request->access_token)->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $order = OrderModel::find($request->order_id);
            $order->otp = rand(1000, 9999);
            $order->otp_verified = false;
            $order->status = 'on_deliver';
            $order->save();

            UserBookingStatus::create([
                'status' => 4,
                'user_id' => $user->id,
                'booking_id' => $order->booking_id
            ]);

            $booking = UserBooking::find($order->booking_id);
            $booking->booking_status = 4;
            $booking->save();

            $oData['order'] = convertNumbersToStrings($order->toArray());
            $oData['booking'] = convertNumbersToStrings($booking->toArray());

            // Driver notification
            $users[] = $order->vendor;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Ready for deliver',
                'message' => 'Booking ' . $booking->booking_number . ' is ready for deliver',
                'imageURL' => '',
                'notificationType' => 'booking_status_change',
                'status' => '4',
                'booking_status' => booking_status(4),
                'booking_number' => $booking->booking_number
            ], true, true);

            // Customer Notification
            $users[] = $booking->customer;
            $this->userFBNotifications->addUserNotification($users, [
                'title' => 'Ready for deliver',
                'message' => 'Booking ' . $booking->booking_number . ' is ready for deliver',
                'imageURL' => '',
                'notificationType' => 'booking_status_change',
                'status' => '4',
                'booking_status' => booking_status(4),
                'booking_number' => $booking->booking_number
            ], true);

            return return_response('1', 200, 'On delivering', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function applyPromoCode(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required',
                'promo_code' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where('user_access_token', $request->access_token)->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $booking = UserBooking::find($request->booking_id);
            if (!$booking) {
                return return_response('0', 200, 'No booking found');
            }

            $coupon = Coupon::whereHas('coupon_for')->where('promo_code', $request->promo_code)->first();
            if ($coupon) {
                $currentDate = Carbon::now();
                if ($currentDate->between($coupon->start_date, $coupon->end_date)) {
                    $selected_types = CouponFor::where('coupon_id', $coupon->id)->get();
                    $selected_types = $selected_types->pluck('vehicle_type_id')->toArray();

                    if (in_array($booking->vehicle_type_id, $selected_types)) {
                        $booking->coupon_applied = 1;
                        $booking->coupon_id = $coupon->id;
                        $booking->save();

                        return return_response('1', 200, 'Coupon applied');
                    } else {
                        return return_response('0', 200, 'Coupon is not applicable');
                    }
                } else {
                    return return_response('0', 200, 'Coupon expired');
                }
            }

            return return_response('0', 200, 'No coupon found');
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function cancelPromoCode(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where('user_access_token', $request->access_token)->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $booking = UserBooking::find($request->booking_id);
            if (!$booking) {
                return return_response('0', 200, 'No booking found');
            }

            $booking->coupon_applied = false;
            $booking->coupon_id = null;
            $booking->save();

            $oData['booking'] = convertNumbersToStrings($booking->toArray());

            return return_response('1', 200, 'Coupon removed', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getOrderDetails(Request $request)
    {
        try {
            $rules = [
                'order_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $order = OrderModel::find($request->order_id);
            if (empty($order)) {
                return return_response('0', 200, 'No order found');
            }

            $oData['order'] = convertNumbersToStrings($order->toArray());

            return return_response('1', 200, 'Order fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function muteAllBookings(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $orders = OrderModel::where('vendor_id', $user->id)->get();
            $order_ids = $orders->pluck('id')->toArray();
            $bookings = UserBooking::whereNotIn('order_id', $order_ids)->orWhereNull('order_id')->get();
            $booking_ids = $bookings->pluck('id')->toArray();
            $muted_bookings = [];
            foreach ($booking_ids as $key => $booking_id) {
                $muted_bookings[$key] = [
                    'booking_id' => $booking_id,
                    'user_id' => $user->id
                ];
            }

            MutedUserBooking::insert($muted_bookings);

            return return_response('1', 200, 'All bookings has been muted');
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function unmuteAllBookings(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            MutedUserBooking::where('user_id', $user->id)->delete();

            return return_response('1', 200, 'All bookings has been unmuted');
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function muteBooking(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            MutedUserBooking::create([
                'booking_id' => $request->booking_id,
                'user_id' => $user->id
            ]);

            return return_response('1', 200, 'Booking muted');
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function unmuteBooking(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'booking_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            MutedUserBooking::where([
                'booking_id' => $request->booking_id,
                'user_id' => $user->id
            ])->delete();

            return return_response('1', 200, 'Booking unmuted');
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }
}
