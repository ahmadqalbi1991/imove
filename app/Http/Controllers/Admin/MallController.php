<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogic\Helpers;
use App\Http\Controllers\Controller;
use App\Models\TypeOfStores;
use App\Traits\StoreImageTrait;
use Illuminate\Http\Request;
use App\Models\Malls;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;
use MStaack\LaravelPostgis\Geometries\Polygon;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;

class MallController extends Controller
{
    use StoreImageTrait, PostgisTrait;

    public function index()
    {
        $page_heading = "Malls & Stores";
        $mode = "List";
        return view('admin.mall.list', compact('mode', 'page_heading'));
    }

    public function create($id = '')
    {
        $page_heading = 'Malls & Stores';
        $mode = "Create";
        $mall_name = '';
        $mall_description = '';
        $mall_image = '';
        $mall_address = '';
        $mall_latitude = '';
        $mall_longitude = '';
        $mall_status = '';
        $type = '';
        $store_mall_id = '';
        if ($id) {
            $id = decrypt($id);
            $role = Malls::find($id);
            $mall_name = $role->mall_name;
            $mall_description = $role->mall_description;
            $mall_image = $role->mall_image;
            $mall_address = $role->mall_address;
            $mall_latitude = $role->mall_latitude;
            $mall_longitude = $role->mall_longitude;
            $mall_status = $role->mall_status;
            $type = $role->type;
            $store_mall_id = $role->store_mall_id;
            $page_heading = ucfirst($type);
            $mode = "Edit";
        }
        $malls = Malls::where('type', 'mall')->where('mall_status', 1)->get();
        $storeTypes = TypeOfStores::where('store_status', 1)->get();
        return view('admin.mall.create', compact('mode', 'page_heading', 'id', 'mall_name', 'malls',
            'mall_status', 'mall_description', 'mall_image', 'mall_address', 'mall_latitude', 'mall_longitude',
            'type', 'store_mall_id', 'storeTypes'));

    }

    public function submit(REQUEST $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
        $o_data['redirect'] = route('malls.list');
        $rules = [
            'mall_name' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $mall_name = $request->mall_name;
            $mall_status = $request->mall_status;
            $mall_description = $request->mall_description;
            $mall_address = $request->mall_address;
            $mall_latitude = $request->latitude;
            $mall_longitude = $request->longitude;
            $type = $request->type;
            $store_mall_id = $request->store_mall_id;
            $id = $request->id;
            $check = Malls::whereRaw('Lower(mall_name) = ?', [strtolower($mall_name)])
                ->where('mall_id', '!=', $id)->get();
            if ($check->count() > 0) {
                $message = ucfirst($type) . " Already Added";
                $errors['mall_name'] = ucfirst($type) . ' Already Added';
            } else {
                if ($id) {
                    DB::beginTransaction();
                    try {
                        $role = Malls::find($id);
                        $mall_image = $request->has('mall_image') ? $this->verifyAndStoreImage($request, 'mall_image', 'malls')
                            : $role->mall_image;
                        $role->mall_name = $mall_name;
                        $role->mall_status = $mall_status;
                        $role->mall_description = $mall_description;
                        $role->mall_image = $mall_image;
                        $role->mall_address = $mall_address;
                        $role->mall_latitude = $mall_latitude;
                        $role->mall_longitude = $mall_longitude;
                        $role->type = $type;
                        $role->store_mall_id = $store_mall_id;
                        $role->coordinates = $request->coordinates ? $this->getCoordinates($request->coordinates) : $role->coordinates;
                        $role->save();


                        DB::commit();
                        $status = "1";
                        $message = ucfirst($type) . " Updated Successfully";

                    } catch (EXCEPTION $e) {
                        DB::rollback();
                        $message = "Failed to update: " . $e->getMessage();
                    }
                } else {
                    DB::beginTransaction();
                    try {
                        $role = new Malls();
                        $role->mall_name = $mall_name;
                        $role->mall_status = $mall_status;
                        $role->mall_description = $mall_description;
                        $role->mall_image = $this->verifyAndStoreImage($request, 'mall_image', 'malls');
                        $role->mall_address = $mall_address;
                        $role->mall_latitude = $mall_latitude;
                        $role->mall_longitude = $mall_longitude;
                        $role->type = $type;
                        $role->store_mall_id = $store_mall_id;
                        $role->coordinates = $request->coordinates ? $this->getCoordinates($request->coordinates) : null;
                        $role->lang_code = 'en';
                        $role->save();


                        DB::commit();
                        $status = "1";
                        $message = ucfirst($type) . " Added Successfully";

                    } catch (EXCEPTION $e) {
                        DB::rollback();
                        $message = "Failed to create: " . $e->getMessage();
                    }
                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function getmallList(Request $request)
    {

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = Malls::select([
            DB::raw('mall_name::text as mall_name'),
            DB::raw('mall_status::text as mall_status'),
            DB::raw('type::text as type'),
            DB::raw('created_at::text as created_at'),
            DB::raw('mall_id::text as mall_id')
        ]);
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);


        $dt->edit('type', function ($data) {
            return ucfirst($data['type']);
        });
        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        $dt->edit('mall_status', function ($data) {
            $checked = ($data["mall_status"] == 1) ? "checked" : "";
            $html = '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                <input type="checkbox" data-role="active-switch"
                    data-href="' . route('malls.status_change', ['id' => encrypt($data['mall_id'])]) . '"
                    ' . $checked . ' >
                <span class="slider round"></span>
            </label>';
            return $html;
        });


        $dt->add('action', function ($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
            if (get_user_permission('malls', 'u')) {
                $html .= '<a class="dropdown-item"
                        href="' . route('malls.edit', ['id' => encrypt($data['mall_id'])]) . '"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
            }
            if (get_user_permission('malls', 'd')) {
                $html .= '<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="' . route('malls.delete', ['id' => encrypt($data['mall_id'])]) . '"><i
                            class="flaticon-delete-1"></i> Delete</a>';
            }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function change_status(REQUEST $request, $id)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $id = decrypt($id);

        $item = Malls::where(['mall_id' => $id])->get();
        if ($item->count() > 0) {
            $item = $item->first();
            Malls::where('mall_id', '=', $id)->update(['mall_status' => $request->status]);
            $status = "1";
            $message = "Status changed successfully";
        } else {
            $message = "Faild to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function delete(REQUEST $request, $id)
    {
        $status = "0";
        $message = "";


        $id = decrypt($id);

        $category_data = Malls::where(['mall_id' => $id])->first();

        if ($category_data) {
            Malls::where(['mall_id' => $id])->delete();
            $message = "mall deleted successfully";
            $status = "1";
        } else {
            $message = "Invalid mall data";
        }

        echo json_encode([
            'status' => $status, 'message' => $message
        ]);
    }

    public function getZone($id = 0)
    {
        $zones = Malls::where('mall_id', $id)->first();
        return Helpers::format_coordiantes($zones->coordinates->getLineStrings() ?? [], true);
    }

    /**
     * @param mixed $value
     * @return Polygon
     */
    public function getCoordinates(mixed $value): Polygon
    {
        foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
            if ($index == 0) {
                $lastcord = explode(',', $single_array);
            }
            $coords = explode(',', $single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }
        $polygon[] = new Point($lastcord[0], $lastcord[1]);
        $coordinates = new Polygon([new LineString($polygon)]);
        return $coordinates;
    }
}
