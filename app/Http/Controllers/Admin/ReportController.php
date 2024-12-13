<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\TruckType;
use App\Models\DriverDetail;
use App\Models\Booking;
use App\Models\Deligate;
use App\Models\BookingQoute;
use Validator;
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
use Mail;

class ReportController extends Controller
{
    public function jobs_in_transit(Request $request){
        $page_heading = "Reports";
        $mode = "Jobs In Transits";
        $bookings = Booking::whereNotIn('status',['pending','qouted','delivered'])->get();
        return view('admin.reports.jobs_in_transit', compact('mode', 'page_heading','bookings'));
    }
}
