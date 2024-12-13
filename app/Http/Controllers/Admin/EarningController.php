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

class EarningController extends Controller
{
    public function index(REQUEST $request)
    {
        $page_heading = "Earnings";
        $mode = "List";
        return view('admin.earnings.list', compact('mode', 'page_heading'));
    }


    public function getearningList($from = null,$to = null){
        $data['from'] = $from;
        $data['to'] = $to;
        $sqlBuilder = Booking::join('users as customers','customers.id','=','bookings.sender_id')->join('categories','categories.id','=','bookings.category_id')->leftJoin('users as companies','companies.id','=','bookings.company_id')->select([
            'bookings.id as id',
            'bookings.booking_number as booking_number',
            'customers.name as customer_name',
            'companies.name as company_name',
            'categories.id as category_id',
            'categories.name as category_name',
            'bookings.status as booking_status',
            'bookings.qouted_amount as qouted_amount',
            'bookings.total_amount as total_amount',
            'bookings.comission_amount as comission_amount',
            'bookings.created_at as created_at',
        ])->where(function($query) use ($data){
            if(isset($data['from']) && isset($data['to'])){
                
                $query->whereBetween('bookings.created_at', [$data['from'], $data['to']]);    
            }
            
        })->orderBy('bookings.id','DESC')->where('bookings.status','delivered')->where('bookings.is_paid','yes');
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);


        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->add('earned_amount', function ($data) {
            $earned_amount = get_earned_amount($data['total_amount'],$data['qouted_amount']);
            return (number_format($earned_amount,3) ?? number_format(0));
        });

        $dt->edit('qouted_amount', function ($data) {
            $html = '';
            $html .= $data['company_name'] ?? 'Not Approved Yet';
            $html .= '<br />';
            $html .= '('.(number_format($data['qouted_amount'],3) ?? number_format(0)).')';
            return $html;
        });

        $dt->edit('comission_amount', function ($data) {
            return $data['comission_amount']."%";
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
                   href="' . route('booking.qoutes', ['id' => encrypt($data['id']), 'type'=>'Earnings']) . '"><i
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
