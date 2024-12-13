<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Booking;
use App\Models\BookingPickUpOrder;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $page_heading = "Dashboard";

        $customerData = DB::table('users')->where('role_id', 3)->whereNull('deleted_at')->where('deleted', '!=', 1)->count();
        $driverData = DB::table('users')->where('role_id', 4)->whereNull('deleted_at')->where('deleted', '!=', 1)->count();
        $bookingData = BookingPickUpOrder::where('payment_status',1)->get()->count();
        $bookingDatacompleted = BookingPickUpOrder::where('payment_status',1)->where('booking_status', '8')->get()->count();

        // get received bookings


        $bookings = DB::table('bookings')
            ->select(DB::raw('created_at as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->get();

        $customerss = DB::table('users')->where('role_id', 3)->orderBy('id', 'DESC')->take(7)->get();


        $drivers = User::join('roles', 'roles.id', '=', 'users.role_id')
            ->join('driver_details', 'driver_details.user_id', '=', 'users.id')->select([
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
            ])->whereNotIn('user_id', function ($query) {
                $query->select('user_id')
                    ->from('blacklists')
                    ->whereColumn('users.id', '=', 'blacklists.user_id');
            })->where('role_id', '=', 2)->count();


        $customersss = DB::table('users')->where('role_id', 3)
            ->select(DB::raw('created_at as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->get();


        // -------- Get the received deliveries ----------

        $received_deliveries = BookingPickUpOrder::select(['order_number', 'id','date', 'contact_person as customer_name'])->where('booking_status', '<=', 8)
            ->orderBy('booking_pick_up_orders.id', 'DESC')->where('payment_status',1)->get();


        // ---------------------------


        return view('admin.dashboard', compact(
            'page_heading',
            'customerData',
            'customersss',
            'driverData',
            'bookings',
            'bookingData',
            'customerss',
            'drivers',
            'bookingDatacompleted',
            'received_deliveries'
        ));
    }
}
