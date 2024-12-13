<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponFor;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function index()
    {
        $page_heading = "Coupons";
        $mode = "List";
        return view('admin.coupons.list', compact('mode', 'page_heading'));
    }

    public function create($id = null)
    {
        $page_heading = "Create Coupon";
        $types = VehicleType::where('status', 1)->get();
        $coupon = null;
        $selected_types = [];
        if ($id) {
            $coupon = Coupon::find($id);
            $selected_types = CouponFor::where('coupon_id', $id)->get();
            $selected_types = $selected_types->pluck('vehicle_type_id')->toArray();
        }

        return view('admin.coupons.create', compact( 'page_heading', 'types', 'coupon', 'selected_types'));
    }

    public function changeStatus(Request $request, $id)
    {
        $status = "1";
        $message = "Status changed";
        $o_data = [];
        $errors = [];

        $item = Coupon::where(['id' => $id])->first();
        $item->is_active = $request->status == '1';

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }
    public function getList(Request $request) {
        $sqlBuilder = Coupon::whereHas('coupon_for')->where('deleted', false);

        return DataTables::of($sqlBuilder)
            ->editColumn('created_at', function ($data) {
                return (new Carbon($data['created_at']))->format('d/m/y H:i A');
            })
            ->addColumn('is_active', function ($data) {
                if (get_user_permission('drivers', 'u')) {
                    $checked = ($data["is_active"]) ? "checked" : "";
                    $html = '<label class="switch s-icons s-outline s-outline-warning mb-4 mr-2">
                                <input type="checkbox" data-role="active-switch"
                                    data-href="' . route('coupons.status_change', ['id' => $data['id']]) . '"
                                    ' . $checked . ' >
                                <span class="slider round"></span>
                            </label>';
                } else {
                    $checked = ($data["status"] == 'active') ? "Active" : "InActive";
                    $class = ($data["status"] == 'active') ? "badge-success" : "badge-danger";
                    $html = '<span class="badge ' . $class . '" ' . $checked . ' </span>';
                }
                return $html;
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="dropdown custom-dropdown">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="flaticon-dot-three"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                if (get_user_permission('drivers', 'u')) {
                    $html .= '<a class="dropdown-item" href="' . route('coupons.edit', ['id' => $data['id']]) . '"><i class="flaticon-pencil-1"></i> Edit</a>';
                }
                if(get_user_permission('drivers','d')){
                    $html.='<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this Driver?"
                        href="'.route('coupons.delete-coupon',['id'=>$data['id']]).'"><i
                            class="flaticon-delete-1"></i> Delete</a>';
                }
                $html .= '</div></div>';
                return $html;
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function deleteCoupon($id) {
        $coupon = Coupon::find($id);
        $coupon->deleted = true;
        $coupon->save();

        echo json_encode(['status' => '1', 'errors' => [], 'message' => 'Coupon deleted', 'oData' => []]);
    }

    public function save(Request $request) {
        $input = $request->except('_token');
        $types = $input['vehicle_type'];
        unset($input['vehicle_type']);
        $exists = false;
        if (!$request->has('id')) {
            $exists = Coupon::where('promo_code', $input['promo_code'])->where('deleted', false)->exists();
        }
        if ($exists) {
            $status = '0';
            $message = 'Coupon already exists';
        } else {
            $input['is_active'] = true;
            if ($request->has('id')) {
                Coupon::where('id', $input['id'])->update($input);
                CouponFor::where('coupon_id', $input['id'])->delete();
                foreach ($types as $type) {
                    CouponFor::create([
                        'coupon_id' => $input['id'],
                        'vehicle_type_id' => $type
                    ]);
                }
            } else {
                $coupon = Coupon::create($input);
                foreach ($types as $type) {
                    CouponFor::create([
                        'coupon_id' => $coupon->id,
                        'vehicle_type_id' => $type
                    ]);
                }
            }

            $status = '1';
            $message = 'Coupon saved successfully';
        }

        echo json_encode(['status' => $status, 'errors' => [], 'message' => $message, 'oData' => []]);
    }
}
