<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\CategoryLanguages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;

class ProductCategoryController extends Controller
{
    public function index(REQUEST $request) {
        $page_heading = "Product Categories";
        $mode="List";
        return view('admin.product-categories.list',compact('page_heading','mode'));
    }

    public function create($id='') {

        $page_heading = "Product Category";
        $category_name = "";
        $category_image = "";
        $category_status = "1";
        $parent_category_id = '';
        $category_icon = '';
        $lang_items=[];
        $mode = "Create";


        if($id) {
            $languages = array_keys(config('languages'));
            $page_heading = "Category";
            $id = decrypt($id);
            $category = ProductCategory::where(['category_id'=>$id])->get()->first();
            $category_name = $category->category_name;
            $category_image = $category->processed_category_image;
            $category_status = $category->category_status;
            $parent_category_id = $category->parent_category_id;
            $category_icon = $category->processed_category_icon;
            $lang_items  = CategoryLanguages::where('category_id_fk','=',$id)->get()->toArray();
            $lang_items = array_column($lang_items,'category_localized_name','lang_code');
            $mode = "Edit";
        }
        $parent_categories = ProductCategory::where(['parent_category_id'=>0])->get();

        return view('admin.product-categories.create',compact('page_heading','mode','category_name','category_image','category_status','id','lang_items','parent_categories','parent_category_id','category_icon'));
    }

    public function submit(REQUEST $request){
        $status  = "0";
        $message = "";
        $o_data  = [];
        $errors  = [];
        $o_data['redirect'] = route('product-categories.list');
        $langs=config('languages');

        $rules = [
            'category_name' => 'required',
            'category_image' => 'image|max:1024',
            'category_icon' => 'image|max:1024'
        ];

        if($request->id ==''){
            $rules['category_image'] = 'required|image|max:1024';
            $rules['category_icon'] = 'required|image|max:1024';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();

        }
        else {
            $file_name= '';

            if($file = $request->file("category_image")){
                $file_name = time().uniqid().".".$file->getClientOriginalExtension();
                $file->storeAs(config('global.category_image_upload_dir'),$file_name,config('global.upload_bucket'));
            }
            $category_icon ='';
            if($file = $request->file("category_icon")){
                $category_icon = time().uniqid().".".$file->getClientOriginalExtension();
                $file->storeAs(config('global.category_image_upload_dir'),$category_icon,config('global.upload_bucket'));
            }

            $id = $request->id;
            $item = ProductCategory::where(['category_name'=>$request->category_name])->where('category_id','!=',$id)->get();

            if($item->count() > 0){
                $message = "category Name already added";
                $errors['category_name'] = "category Name already added";
            }
            else {
                if($id > 0){
                    DB::beginTransaction();
                    try{
                        $item = ProductCategory::where(['category_id'=>$id])->get()->first();
                        $item->updated_at = gmdate('Y-m-d H:i:s');
                        $item->category_name = $request->category_name;
                        $item->unique_category_text = create_plink($request->category_name);
                        $item->parent_category_id = $request->parent_category_id??0;
                        if($file_name != ''){
                            $item->category_image = $file_name;
                        }
                        if($category_icon){
                            $item->category_icon = $category_icon;
                        }
                        $item->category_status = $request->category_status;
                        $item->save();

                        if(count($langs) > 1) {
                            foreach($langs as $key=>$value ){
                                if($key != 'en' && $request->{"category_name_".$key} != '' ) {
                                    $ins = [
                                        'category_localized_name'          => $request->{"category_name_".$key}??$request->category_name,
                                        'lang_code'     => $key,
                                        'category_id_fk'      => $id
                                    ];
                                    CategoryLanguages::create($ins);
                                }
                            }
                        }
                        DB::commit();
                        $status = "1";
                        $message = "Category Updated Successfully";
                    }
                    catch(EXCEPTION $e){
                        DB::rollback();
                        $message = "Faild to create category ".$e->getMessage();
                    }
                }
                else{
                    $ins=[];
                    $unique_code = strtoupper( str_replace(" ","",$request->category_name).uniqid() );

                    $ins = [
                        'category_name'          => $request->category_name,
                        'unique_category_code'   => $unique_code,
                        'parent_category_id'  =>   $request->parent_category_id??0,
                        'unique_category_text'   => create_plink($request->category_name),
                        'category_image'         => $file_name,
                        'category_icon'          => $category_icon,
                        'created_by' => Auth::user()->id,
                        'category_status'        => $request->category_status
                    ];

                    if(!empty($ins)){
                        $local_ins=[];
                        DB::beginTransaction();
                        try{
                            $category = ProductCategory::create($ins);
                            if(count($langs) > 1) {

                                foreach($langs as $key=>$value ){

                                    if($key != 'en' && $request->{"category_name_".$key} != '' ) {

                                        $local_ins[] = [
                                            'category_localized_name'          => $request->{"category_name_".$key}??$request->category_name,
                                            'lang_code'     => $key,
                                            'category_id_fk'      => $category->category_id
                                        ];

                                    }
                                }
                            }
                            if($local_ins)
                                CategoryLanguages::insert($local_ins);

                            DB::commit();
                            $status = "1";
                            $message = "category Created Successfully";
                        }
                        catch(EXCEPTION $e){
                            DB::rollback();
                            $message = "Faild to create category ".$e->getMessage();
                        }
                    }
                }
            }
        }
        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function getCategoryList(Request $request){

        $sqlBuilder = ProductCategory::select([
            'product_categories.category_image',
            'product_categories.category_name',
            'p_category.category_name as parent_category_name',
            DB::raw('product_categories.category_status::text as category_status'),
            DB::raw('product_categories.created_at::text as created_at'),
            DB::raw('product_categories.category_id::text as category_id')
        ])->leftJoin('product_categories as p_category',function($join){
            $join->on('p_category.category_id','=','product_categories.parent_category_id');
        });
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('category_image', function($data){
            return "
            <ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>
                <li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-xs pull-up' aria-label='Sophia Wilkerson'  data-bs-original-title='Sophia Wilkerson'>
                    <img class='rounded-circle' src='".get_uploaded_image_url($data['category_image'],'category_image_upload_dir')."' style='width:50px; height:50px;'>
                </li>
            </ul>";
        });

        $dt->edit('created_at',function($data){
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('category_status',function($data){
            $checked = ($data["category_status"]==1)?"checked":"";
            $html= '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                <input type="checkbox" data-role="active-switch"
                    data-href="'.route('category.status_change', ['id' => encrypt($data['category_id'])]).'"
                    '.$checked.' >
                <span class="slider round"></span>
            </label>';
            return $html;
        });


        $dt->add('action', function($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
            if(get_user_permission('category','u')){
                $html.='<a class="dropdown-item"
                        href="'.route('category.edit',['id'=>encrypt($data['category_id'])]).'"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
            }
            if(get_user_permission('category','d')){
                $html.='<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="'.route('category.delete',['id'=>encrypt($data['category_id'])]).'"><i
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

        $item = ProductCategory::where(['category_id'=>$id])->get();
        if($item->count() > 0){
            $item=$item->first();
            ProductCategory::where('category_id','=',$id)->update(['category_status'=>$request->status]);
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

        $category_data = ProductCategory::where(['category_id' => $id])->first();

        if( $category_data ) {
            ProductCategory::where(['category_id' => $id])->delete();
            $message = "category deleted successfully";
            $status = "1";
        }
        else {
            $message = "Invalid category data";
        }

        echo json_encode([
            'status' => $status , 'message' => $message
        ]);
    }
}
