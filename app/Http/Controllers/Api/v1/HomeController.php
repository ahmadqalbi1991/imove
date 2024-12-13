<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorDetailsModel;
use App\Models\User;
use App\Models\Likes;
use App\Models\BannerModel;
use App\Models\Categories;
use App\Models\ProductModel;
use App\Models\Service;
use App\Models\FeaturedProducts;
use App\Models\Rating;
use App\Models\FeaturedProductsImg;
use App\Models\ServiceCategories;
use App\Models\Category;
use App\Models\ServicePrice;
use App\Models\PromotionBanners;
use Validator,DB;

class HomeController extends Controller
{

    public function get_promotions(REQUEST $request){
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
        $list = PromotionBanners::where(['status'=>1])->orderBy('id','desc')->get();
        if($list->count() > 0){
            $status = "1";
            $message = "data listed";
            $o_data['list'] = convert_all_elements_to_string($list->toArray());
        }else{
            $message = "no data to list";
        }
        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => (object)$o_data], 200);
    }
    public function get_home_search(REQUEST $request){
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $validator = Validator::make($request->all(), [
            'search_key'=>'required',
        ],[
            'search_key.required'=>'Search key required'
        ]);

        if ($validator->fails()) {
            $message = "Validation error occured";
            $errors = $validator->messages();
        }else{
            $search_key = $request->search_key;
            $page = $request->page??1;
            $limit=$request->limit??10;
            $offset = ($page - 1) * $limit;

            // $model1Query = VendorDetailsModel::select(['id', 'company_name as name'])->addSelect(DB::raw("'store' as nav_type"))->addSelect(DB::raw("'own' as store_name"));
            // $model2Query = Service::select(['service.id', 'name','vendor_details.company_name as store_name'])->join('vendor_services','vendor_services.service_id','=','service.id')->join('vendor_details','vendor_details.id','=','vendor_services.vendor_id')->addSelect(DB::raw("'service' as nav_type"));
            // $model3Query = ProductModel::select(['product.id', 'product_name as name','vendor_details.company_name as store_name'])->join('vendor_details','vendor_details.id','=','product.product_vender_id')->addSelect(DB::raw("'product' as nav_type"));
            $category_filter = ServiceCategories::join('service_category_selected','service_category_selected.category_id','=','service_category.id')->select('*')->whereRaw("lower(name) ilike '%".strtolower($search_key)."%' ")->get();
            $filter_sevice_ids = [];
            if($category_filter->count() > 0){
                $category_filter = $category_filter->toArray();
                $filter_sevice_ids = array_column($category_filter,'service_id');
            }
            

            $model1Query = VendorDetailsModel::select(['user_id as id', 'company_name as name'])->addSelect(DB::raw("'own' as store_name"))->addSelect(DB::raw("'store' as nav_type"))->join('users','users.id','=','vendor_details.user_id')->whereNotIn('users.activity_id',[1,6,4]);
            $model2Query = Service::select(['service.id', 'service.name','vendor_details.company_name as store_name'])->addSelect(DB::raw("'service' as nav_type"))->join('vendor_services','vendor_services.service_id','=','service.id')->leftjoin('vendor_details','vendor_details.user_id','=','vendor_services.vendor_id')->where(['service.active'=>1,'service.deleted'=>0])->distinct('service.id');
            $model3Query = ProductModel::select(['product.id', 'product_name as name','vendor_details.company_name as store_name'])->leftJoin('vendor_details','vendor_details.user_id','=','product.product_vender_id')->addSelect(DB::raw("'product' as nav_type"))->where(['product.deleted'=>0,'product.product_status'=>1]);
            
            $main_service_search_result = $model2Query->get()->toArray();
            $selcted_ids = array_column($main_service_search_result,'id');
            $model4Query = Service::select(['service.id', 'service.name','vendor_details.company_name as store_name'])->addSelect(DB::raw("'service' as nav_type"))->join('vendor_services','vendor_services.service_id','=','service.id')->leftjoin('vendor_details','vendor_details.user_id','=','vendor_services.vendor_id')->where(['service.active'=>1,'service.deleted'=>0])->whereIn('service.id',$filter_sevice_ids)->whereNotIn('service.id',$selcted_ids);
            
            if($search_key != ''){
                $model1Query->whereRaw("lower(company_name) ilike '%".strtolower($search_key)."%' ");
            }
            if($search_key != ''){
                $model2Query->whereRaw("lower(name) ilike '%".strtolower($search_key)."%' ");
            }
            if($search_key != ''){
                $model3Query->whereRaw("lower(product_name) ilike '%".strtolower($search_key)."%' ");
            }
            
            
            $unionedQuery = $model1Query->union($model2Query)->union($model3Query)->union($model4Query);
            //echo $unionedQuery->toSql(); 
            $results = DB::table(DB::raw("({$unionedQuery->toSql()}) as combined"))
                        ->mergeBindings($unionedQuery->getQuery())->take($limit)->skip($offset)
                        ->get();
            if($results->count() > 0){
                $result = [];
                foreach($results as $item){
                    $resp = ['id'=>$item->id,'name'=>$item->name,'type'=>$item->nav_type];
                    if($item->nav_type=='product' || $item->nav_type == "service"){
                        $resp['display_name'] = $item->name."- ".$item->store_name;
                    }else{
                        $resp['display_name'] = $item->name;
                    }
                    $result[]= $resp;
                }
                $o_data['list'] = convert_all_elements_to_string($result);
                $status = "1";
                $message = "data fetched successfully";
            }else{
                $message = "no data to list";
            }
            
        }


        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => (object)$o_data], 200);
    }
    function index(Request $request)
    {
        $status = (string) 1;
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), []);

        if ($validator->fails()) {
            $message = "Validation error occured";
            $errors = $validator->messages();
            return response()->json([
                'status' => (string) 0,
                'message' => $message,
                'errors' => (object) $errors,
            ], 200);
        }

        $access_token = $request->access_token;
        $user = User::where('user_access_token', $access_token)->first();
        $limit = isset($request->limit) ? $request->limit : 10;
        $offset = isset($request->page) ? ($request->page - 1) * $request->limit : 0;

        $where = [];
        $params = [];
        $filter['search_text'] = $request->search_text;
        $filter['emirate_id']  = $request->emirate_id;
        $filter['city_id']     = $request->city_id;


        //banner 
       
        $banner  = BannerModel::select('id','banner_title','banner_image')->where(['active' => 1])->get();
        foreach ($banner as $key => $val) {
            $banner[$key]->banner_image = url(config('global.upload_path') . config('global.banner_image_upload_dir') . $val->banner_image);
           
        }

        $categories  = Category::select('*')->where(['status' => 'active'])->get();
       
        

        $o_data['banner']  = $banner;
        $o_data['categories']  = $categories;
        $o_data = convert_all_elements_to_string($o_data);
        if(!empty($banner) && count($banner) == 0)
        {
            $o_data['banner']  = [];
        }

        return response()->json(['status' => $status, 'message' => $message, 'errors' => (object) $errors, 'oData' => $o_data], 200);
    }
}