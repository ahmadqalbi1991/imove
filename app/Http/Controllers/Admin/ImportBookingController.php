<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\TruckType;
use App\Models\DriverDetail;
use App\Models\Booking;
use App\Models\BookingStatusTracking;
use App\Models\BookingQoute;
use App\Models\Deligate;
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
use App\Mail\CustomerAccountMail;
use Mail;

class ImportBookingController extends Controller
{
    public function index(){

        $mode = 'import';
        $page_heading = 'Import bookings';
        return view('admin.bookings.import',compact('mode','page_heading'));
    }

    public function download_csv(){

        $truck = TruckType::where('status' , 'active')->get();

        foreach ($truck as $items) {
            $data[] = $items->truck_type;
        }

        // foreach($truck as $items){
        //     $data[] = $items->truck_type;
        // }
        // dd($data);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=bookings.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['customer_name','customer_email','truck_type_id', 'driver_emails','quantity', 'collection_address','deliver_address','receiver_name','receiver_email','receiver_phone','commission_amount','is_paid']);
        fputcsv($output, ['John Martin','johnmarting@email.com','1', 'driver1@email.com,driver2@email.com','1', 'Po Box 30499 Jebel Ali Free Zone','P.O. Box: 23318, SHARJAH U A E','Allen','allen@email.com','971 8858063','0.2','yes/no']);

    }

    public function import(Request $request){
        
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt,xls,xlsx'
        ]);
        $message_error = [];
        $success_records = 0;
        if($request->has('csv')){
            $custom_file_name = time().'-'.$request->file('csv')->getClientOriginalName();
            $file = $request->file('csv')
            ->storeAs('bookings/csvs/',$custom_file_name,'public');
            
            $read = fopen(public_path('storage/bookings/csvs/'.$custom_file_name),"r");
           
            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($read, 1000, ",")) !== FALSE) {
               $num = count($filedata);
              
             if($i == 0){
                $i++;
                continue; 
             }
               for ($c=0; $c < $num; $c++) {
                  $importData_arr[$i][] = $filedata [$c];
               }
               $i++;
            }
            fclose($read);

            foreach($importData_arr as $row => $importData){
                    
                    if(!(User::where('email',$importData[1])->exists())){
                        //$message_error[] = 'Customer email is not valid at row number '.($row+1).' in csv file.';
               
                        $new_user = new User();
                        $new_user->name = $importData[0] ?? 'New Timex User'; 
                        $new_user->email = $importData[1];
                        $new_user->password = Hash::make('timex_user');
                        $new_user->role_id = 3;
                        $new_user->status = "active";
                        $new_user->save();

                        if(!empty($new_user)){
                            $token = encrypt($new_user->id);
                            $new_user->token = $token;
                           $id = DB::table('user_password_resets')->insertGetId(['email' => $new_user->email, 'token' => $token, 'is_valid' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                           if(!empty($id)){
                                if(env('MAILS_ENABLE')){
                                    Mail::to($new_user->email)->send(new CustomerAccountMail($new_user));
                                } 
                           }    

                        }

                    }  

                    $user = User::where('email',$importData[1])->where('role_id',3)->first();             
                    $deligate = Deligate::where('slug','truck')->first();
                    try {
                        $booking = Booking::create([
                            'collection_address' => $importData[5],
                            'deliver_address' => $importData[6],
                            'sender_id' => $user->id,
                            'receiver_name' => $importData[7],
                            'receiver_email'=> $importData[8],
                            'receiver_phone' => $importData[9],
                            'deligate_id' => $deligate->id,
                            'deligate_details' => 'truck',
                            'truck_type_id' => $importData[2],
                            'quantity' => $importData[4],
                            'admin_response' => 'pending',
                            'status' => 'pending',
                            'is_paid' => $importData[11],
                            'comission_amount' => $importData[10]
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

                        $drivers = explode(',',$importData[3]);
                        $drivers = User::whereIn('email',$drivers)->where('role_id',2)->get();

                        if(!empty($booking) && count($drivers) > 0){
                            
                            foreach($drivers as $driver){
                                
                                $booking_qoute = new BookingQoute();
                                $booking_qoute->booking_id = $booking->id;
                                $booking_qoute->driver_id = $driver->id;
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
                            $booking->admin_response = 'ask_for_qoute';
                            $booking->save();
                            $data['user'] = User::find($booking->sender_id);
                            $data['booking'] = $booking;
                            $booking->admin_response = 'ask_for_qoute';
                            if(env('MAILS_ENABLE')){
                                Mail::to($data['user']->email)->send(new CustomerRequestMail($data));
                            } 
                        }else{
                            $message_error[] = 'Drivers could not assign at row number '.($row+1).' in csv file.';    
                        }

                        if(!empty($booking) && !empty($booking_qoute)){
                           
                            $success_records++;
                           
                        }
                            
                    }
                    catch (\Exception $e) {
                        $message_error[] = 'Data is not valid at row number '.($row+1).' in csv file.'; 
                    }
                   
            }
            if($success_records > 0){
                return response()->json(['message' => 'Bookings imported successfully','message_error' => $message_error,'success_records' => $success_records],200);
            }else{
                return response()->json(['message' => 'Bookings could not imported','message_error' => $message_error,'success_records' => $success_records],200);    
            }
            
        }
        return response()->json(['message' => 'Please select the file to upload'],401);
    }
}
