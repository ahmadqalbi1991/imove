<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TempUser;
use Illuminate\Support\Facades\Mail;
use App\Models\BookingPickUpOrder;
use App\Models\BookingDropOffOrder;
use App\Models\RequestImages;
use App\Models\Category;
use App\Models\Care;
use App\Models\Size;

class SendBookingStatusEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:send_booking_status_email {booking_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Verification Email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id =  $this->argument('booking_id');

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
        
        $user=User::find($datamain->customer_id);
        
        Mail::send('email_templates.booking', compact('user','page_heading', 'mode', 'id', 'name', 'cost', 'status', 'route_back', 'categories', 'sizes', 'delivery_type', 'category_id', 'size_id', 'delivery_type_selected', 'customers', 'customer_id', 'cares','datamain','drivers'), function($message) use ($user) {
            $message->to($user->email);
           // $message->to('sabeeh.hashmi2@gmail.com');
            $message->subject('Booking Status Updated');
            
            
        });
    }
}
