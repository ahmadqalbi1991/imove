<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Settings;
use App\Models\States;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;

class PageController extends Controller
{
    public function index(){
        $page_heading = "Pages";
        $mode="List";
        return view('admin.cms.pages.list',compact('mode','page_heading'));
    }

    public function create($id=''){
        $page_heading = 'Pages';
        $mode="Create";
        $cms_page = new Page();

        if($id){
            $mode= "Edit";
            $id = decrypt($id);
            $cms_page = Page::find($id);
        }
        $page_type_ids = Page::select('slug')->distinct()->get()->pluck('slug')->toArray();
        $route_back = route('notification.list');

        return view('admin.cms.pages.create',compact('mode','page_heading','id',
            'cms_page','page_type_ids','route_back'));

    }

    public function submit(REQUEST $request){
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('cms.pages.list');
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'slug' => 'required',
            'status' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $id         = $request->id;
            $check      = Page::whereRaw('Lower(slug) = ?',[strtolower($request->slug)])->where('id','!=',$id)->get();
            if($check->count() > 0){
                $message = "Language Already Addded";
                $errors['language_name'] = 'Page Already Added';
            }else{
                if($id){
                    DB::beginTransaction();
                    try{
                        $cms_page   = Page::find($id);
                        $cms_page->title    = $request->title;
                        $cms_page->description  = $request->description;
                        $cms_page->slug       = $request->slug;
                        $cms_page->status       = $request->status;
                        $cms_page->save();

                        DB::commit();
                        $status = "1";
                        $message = "Page updated Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to update language ".$e->getMessage();
                    }
                }else{
                    DB::beginTransaction();
                    try{
                        $cms_page   = new Page();
                        $cms_page->title    = $request->title;
                        $cms_page->description  = $request->description;
                        $cms_page->slug       = $request->slug;
                        $cms_page->status       = $request->status;
                        $cms_page->save();

                        DB::commit();
                        $status = "1";
                        $message = "Page Added Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Failed to create page ".$e->getMessage();
                    }
                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function settings(){
        $page_heading = 'Settings';
        $mode="Settings";
        $datamain = [];

            $datamain = Settings::find('1');
     
        

        return view('admin.cms.pages.settings',compact('mode','page_heading','datamain'));

    }

    public function Settingssave(REQUEST $request){
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('cms.pages.settings');
        $rules = [
            'service_charge' => 'required',
            'tax_percentage' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $id         = $request->id;
            
                if($id){
                    DB::beginTransaction();
                    try{
                        $cms_page   = Settings::find($id);
                        $cms_page->service_charge    = $request->service_charge;
                        $cms_page->tax_percentage  = $request->tax_percentage;
                        $cms_page->save();

                        DB::commit();
                        $status = "1";
                        $message = "Settings updated Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to update language ".$e->getMessage();
                    }
                }else{
                    DB::beginTransaction();
                    try{
                        $cms_page   = new Settings();
                        $cms_page->service_charge    = $request->service_charge;
                        $cms_page->tax_percentage  = $request->tax_percentage;
                        $cms_page->save();

                        DB::commit();
                        $status = "1";
                        $message = "Settings Added Successfully";

                    }catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Failed to create page ".$e->getMessage();
                    }
                }
            
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function Emirates(){

        $page_heading = 'Emirates';
        $mode="Emirates Delivery Charges";
        $datamain = [];

            $datamain = States::get();
     
        

        return view('admin.cms.pages.emirates',compact('mode','page_heading','datamain'));

    }

    public function EmiratesSave(REQUEST $request){
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('cms.pages.emirates');
        $rules = [
            // 'service_charge' => 'required',
            // 'tax_percentage' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        }
        else {
            $id         = $request->id;
            $amount      = $request->amount;
            $sl_id      = $request->sl_id;


            foreach ($request->state as $key => $value) {
                $datains = States::find($sl_id[$key]);
                if(empty($sl_id[$key]))
                {
                    $datains = new States;  
                }
                
                $datains->name = $value;
                $datains->active = 1;
                $datains->country_id = 1;
                $datains->amount = $amount[$key];
                $datains->save();
            }
            $status = "1";
            $message = "Emirates updated Successfully";
            
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }
    public function getPagesList(Request $request){

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = Page::select([
            DB::raw('title::text as title'),
            DB::raw('slug::text as slug'),
            DB::raw('created_at::text as created_at'),
            DB::raw('id::text as id'),
            DB::raw('status::integer as status'),
            DB::raw('status::integer as status_text')
        ]);
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('slug',function($data){
            return Page::PageType[$data['slug']] ?? '';
        });

        $dt->edit('status_text', function ($data) {
            $statusTextHtml = '';
            if ($data["status"] == 1) {
                $statusTextHtml = '<div class="ticket active">
                <i class="fas fa-check-circle text-success"></i> Active </div>';
            } else {
                $statusTextHtml = '<div class="ticket disabled">
                <i class="fas fa-times-circle text-danger"></i> Disabled
                </div>';
            }
            return $statusTextHtml;
        });

        $dt->edit('created_at',function($data){
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('status',function($data){
            $checked = ($data["status"]==1)?"checked":"";
            $html = '';
            if(get_user_permission('pages','u')){
            $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                <input type="checkbox" data-role="active-switch"
                    data-href="'.route('cms.pages.status_change', ['id' => encrypt($data['id'])]).'"
                    '.$checked.' >
                <span class="slider round"></span>
            </label>';
            }else{
                $checked = ($data["status"]==1)?"Active":"Inactive";
                $color = ($data["status"]==1)?"success":"secondary";
                $html = '<span class = "badge badge-'.$color.'">'.$checked.'</span>';
            }
            return $html;
        });


        $dt->add('action', function($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
            if(get_user_permission('pages','u')){
                $html.='<a class="dropdown-item"
                        href="'.route('cms.pages.edit',['id'=>encrypt($data['id'])]).'"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
            }
            if(get_user_permission('pages','d')){
                $html.='<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="'.route('cms.pages.delete',['id'=>encrypt($data['id'])]).'"><i
                            class="flaticon-delete-1"></i> Delete</a>';
            }
            $html.='</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function change_status(REQUEST $request,$id){
        $status = "0";
        $message = "";
        $o_data  = [];
        $errors = [];

        $id = decrypt($id);

        $item = Page::where(['id'=>$id])->get();
        if($item->count() > 0){
            $item=$item->first();
            $item->status = $request->status;
            $item->save();
            $status = "1";
            $message= "Status changed successfully";
        }else{
            $message = "Failed to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function delete(REQUEST $request,$id) {
        $status = "0";
        $message = "";


        $id = decrypt( $id );

        $category_data = Page::where(['id' => $id])->first();

        if( $category_data ) {
            Page::where(['id' => $id])->delete();
            $message = "Page deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid page identifier";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
