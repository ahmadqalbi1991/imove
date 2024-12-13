<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\NewNotification;
use Kreait\Firebase\Contract\Database;


class NotificationController extends Controller
{
    //
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
        public function index(){
            $page_heading = "Notifications";
            $mode="List";
            return view('admin.notificaiton.list',compact('mode', 'page_heading'));
        }

        public function getNotiList(Request $request){


            $sqlBuilder = NewNotification::select([
                // 'customer_name',DB::raw('SELECT email FROM users Where id = new_notifications.user_id'),
                DB::raw('title::text as title'),
                DB::raw('description::text as customer_desc'),
                DB::raw('new_notifications.status::text as status'),
                DB::raw('new_notifications.status::text as status_text'),
                DB::raw('new_notifications.created_at::text as created_at'),
                DB::raw('new_notifications.id as id')
            ])->orderBy('id','desc');
            $dt = new Datatables(new LaravelAdapter);
    
            $dt->query($sqlBuilder);
    
            $dt->edit('created_at',function($data){
                return (new Carbon($data['created_at']))->format('d/m/y H:i A');
            });

            $dt->edit('status_text', function ($data) {
                $statusTextHtml = '';
                if ($data["status"] == 'active') {
                    $statusTextHtml = '<div class="ticket active">
                    <i class="fas fa-check-circle text-success"></i>'. ucfirst($data["status"]).' </div>';
                } else {
                    $statusTextHtml = '<div class="ticket disabled">
                    <i class="fas fa-times-circle text-danger"></i> Disabled
                    </div>';
                }
                return $statusTextHtml;
                
            });

            $dt->edit('status',function($data){
                if(get_user_permission('notifications','u')){
                    $checked = ($data["status"]=='active')?"checked":"";
                        $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                            <input type="checkbox" data-role="active-switch"
                                data-href="'.route('notification.status_change', ['id' => encrypt($data['id'])]).'"
                                '.$checked.' >
                            <span class="slider round"></span>
                        </label>';
                }else{
                    $checked = ($data["status"]=='active')?"Active":"InActive";
                    $class = ($data["status"]=='active')?"badge-success":"badge-danger";
                    $html = '<span class="badge '.$class.'" '.$checked.' </span>';
                }
              return $html;
            });
    

            $dt->edit('customer_desc',function($data){
                $desciption = substr($data['customer_desc'],0,50).'...!';
                return $desciption;
            });
    
    
            $dt->add('action', function($data) {
                $html = '<div class="dropdown custom-dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="flaticon-dot-three"></i>
                    </a>
    
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                        if(get_user_permission('notifications','u')){
                        $html.='<a class="dropdown-item"
                            href="'.route('notification.edit',['id'=>encrypt($data['id'])]).'"><i
                                class="flaticon-pencil-1"></i> Edit</a>';
                        }
                        
                        if(get_user_permission('notifications','d')){
                            $html.='<a class="dropdown-item" data-role="unlink"
                                data-message="Do you want to remove this notification?"
                                href="'.route('notification.delete',['id'=>encrypt($data['id'])]).'"><i
                                    class="flaticon-delete-1"></i> Delete</a>';
                            }                             
                        
    
                $html.='</div>
                </div>';
                return $html;
            });
    
            return $dt->generate();
            
        }

        public function create($id=''){
            $page_heading = 'Notification Create';
            $mode = "Create";
            $site_modules = config('crud.site_modules');
            $operations   = config('crud.operations');

            
            $users = User::where('role_id','!=',1)
            ->whereNull('deleted_at')->get();
            $route_back = route('notification.list');

            return view('admin.notificaiton.create',compact('mode', 'page_heading','operations','site_modules','users','route_back'));
    
        }


        public function edit($id){
            $id = decrypt($id);


            $noti = NewNotification::find($id);
            $noti->image = url(Storage::url('notificaiton/'.$noti->image));
            $id = $noti->id;
            $title = $noti->title;
            $desc = $noti->description;
            $image = $noti->image;
            $status = $noti->status;

            $page_heading = 'Notification Edit';
            $mode = "Detail";
            $site_modules = config('crud.site_modules');
            $operations   = config('crud.operations');
            $route_back = route('notification.list');
            
            return view('admin.notificaiton.edit',compact('id','mode', 'page_heading','operations','site_modules','title','desc','image','status','route_back'));
    
        }



        public function submit(REQUEST $request){
           
            $status     = "0";
            $message    = "";
            $o_data     = [];
            $errors     = [];
            $o_data['redirect'] = route('notification.list');
            $rules = [
                'title' => 'required',
                'desc' => 'required',
                'status' => 'required',
                
            ];
    
            $validator = Validator::make($request->all(),$rules);
    
            if ($validator->fails()) {
                $status = "0";
                $message = "Validation error occured";
                $errors = $validator->messages();
            }
            else {


                $noti = new NewNotification();
                $noti->user_id = 0;
                $noti->title = $request->title;
                $noti->status = $request->status;
                $noti->description = $request->desc;

                $response = image_upload($request,'notificaiton','image');
                        
                if($response['status']){
                    $noti->image= $response['link'];
                }
                
                $noti->save();


                $datamain = new User;

                $image = '';
                if(!empty($noti->image))
                {
                    $image = asset('storage/notificaiton/'.$noti->image);
                }
                if(!empty($request->usertype))
                {
                    $datamain = $datamain->where('role_id',$request->usertype);
                }

                if(!empty($request->options))
                {
                    $datamain = $datamain->whereIn('id',$request->options);

                }
                $datamain = $datamain->get();

                foreach ($datamain as $key => $value) {
                  
           
             $title = $request->title;

             $description = $request->desc;
             $notification_id = time();
             $ntype = 'public-notification';
             $notification_data = [];
             if (!empty($value->firebase_user_key)) {
                 $notification_data["Notifications/" . $value->firebase_user_key . "/" . $notification_id] = [
                     "title" => $title,
                     "description" => $description,
                     "notificationType" => $ntype,
                     "createdAt" => gmdate("d-m-Y H:i:s", $notification_id),
                     "url" => "",
                     "imageURL" => $image,
                     "read" => "0",
                     "seen" => "0",
                 ];
                 $this->database->getReference()->update($notification_data);
             }
             $res = [];
             if (!empty($value->user_device_token)) {
                
                 $res = send_single_notification(
                     $value->user_device_token,
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
                         "imageURL" => $image,
                     ]
                 );
              
             }
                }

            
                  
                       
                  

                   // exec("php ".base_path()."/artisan send:admin_notification --uri=" . $request->usertype." --uri2=" . $noti->id . " --uri3=" . $noti->image . " > /dev/null 2>&1 & ");


                $status = "1";
                $message = "Notification Send Successfully";
    
        }
            echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function getListUser(Request $request){
        $option = $request->option;
        if($option == 0){

            $output  = "";
            $datas = User::whereIn('role_id', ['3', '4'])
             ->whereNull('deleted_at') // Ensures soft-deleted users are excluded
             ->get();
    
            foreach($datas as $data){

                $output .= 
                    "<tr>
                        <td> " .
                            "<span class='custom-checkbox'>"
                                . "<input type='checkbox' id='checkbox1' class='select-customers' name='options[]' value='$data->id'> 
                            </span> 
                        </td>



                        <td> " . $data->name . "</td>
                        <td> " . $data->email . "</td>
                    </tr>";
            }
        }else{

            $output  = "";
            $datas = User::where('role_id' , $request->option)
            ->whereNull('deleted_at')->get();
    
            foreach($datas as $data){
                $output .= 
                    "<tr>
                        <td> " .
                            "<span class='custom-checkbox'>"
                                . "<input type='checkbox' id='checkbox1' class='select-customers' name='options[]' value=' $data->id '> 
                            </span> 
                        </td>

                        <td> " . $data->name . "</td>
                        <td> " . $data->email . "</td>
                    </tr>";
            }
        }
        
        return response($output);
        
    }

    public function getSearchUser(Request $request){
        $option = $request->option; 
        $search = $request->search;
        if($option == 0){

            $output  = "";
            $datas = User::where('name', 'like', '%'.$search.'%')->get();
    
            foreach($datas as $data){

                $output .= 
                    "<tr>
                        <td> " .
                            "<span class='custom-checkbox'>"
                                . "<input type='checkbox' id='checkbox1' name='options[]' value='$data->id'> 
                            </span> 
                        </td>



                        <td> " . $data->name . "</td>
                        <td> " . $data->email . "</td>
                    </tr>";
            }
        }else{

            $output  = "";
            $datas = User::where('name', 'like', '%'.$search.'%')->where('role_id' , $request->option)->get();
    
            foreach($datas as $data){
                $output .= 
                    "<tr>
                        <td> " .
                            "<span class='custom-checkbox'>"
                                . "<input type='checkbox' id='checkbox1' name='options[]' value=' $data->id '> 
                            </span> 
                        </td>

                        <td> " . $data->name . "</td>
                        <td> " . $data->email . "</td>
                    </tr>";
            }
        }
        
        return response($output);
        
    }


    public function update(REQUEST $request){
        
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('notification.list');
       
            $title  = $request->title;
            $desc= $request->desc;
            $id         = $request->id;
            $status         = $request->status;
            $image         = $request->image;
           
                if($id){

                    $rules = [
                        'title' => 'required',
                        'desc' => 'required',
                        'status' => 'required',
                    ];
            
                    $validator = Validator::make($request->all(),$rules);
            
                    if ($validator->fails()) {
                        $status = "0";
                        $message = "Validation error occured";
                        $errors = $validator->messages();
                    }
                    else {

                        if($request->file("image") != null ){
                            $noti   = NewNotification::find($id);
                            $noti->title    = $title;
                            $noti->description  = $desc;
                            $noti->status  = $status;

                                $response = image_upload($request,'notificaiton','image');
                                
                                if($response['status']){
                                    $noti->image= $response['link'];
                                }
                                
                                $noti->save();
                        }
                        else{
                            $noti   = NewNotification::find($id);
                            $noti->title    = $title;
                            $noti->description  = $desc;
                            $noti->status  = $status;
                            $noti->save();
                        }



                        


                        $status = "1";
                        $message = "Notification Updated Successfully";
                    }
                }
        

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }
    
    
    public function change_status(REQUEST $request,$id){
        $status = "0";
        $message = "";
        $o_data  = [];
        $errors = [];

        $id = decrypt($id);

        $item = NewNotification::where(['id'=>$id])->get();
 
        if($item->count() > 0){

            NewNotification::where('id','=',$id)->update(['status'=>$request->status == '1'?'active':'inactive']);
            $status = "1";
            $message= "Status changed successfully";
        }else{
            $message = "Faild to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function delete(REQUEST $request,$id) {
        $status = "0";
        $message = "";

        $id = decrypt( $id );

        $noti = NewNotification::where(['id' => $id])->first();

        if( $noti ) {
            NewNotification::where(['id' => $id])->delete();
            $message = "Notification deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid Notification data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
