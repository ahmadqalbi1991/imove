<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Malls;
use App\Models\Product;
use App\Traits\StoreImageTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;

class ProductController extends Controller
{
    use StoreImageTrait;

    public function index()
    {
        $page_heading = "Products";
        $mode="List";
        return view('admin.products.list', compact('mode', 'page_heading'));
    }

    public function create($id = null)
    {
        $page_heading = 'Product';
        $mode="Create";
        $product = new Product();
        if ($id) {
            $mode = "Edit";
            $id = decrypt($id);
            $_product = Product::find($id);
            $product->fill($_product->toArray());

        }
        $stores = Malls::where('type', '=', 'store')->get();
        $categories = ProductCategory::select(['category_id', 'category_name'])->where('category_status', '=', 1)->get();
        return view('admin.products.create', compact('mode', 'page_heading', 'id', 'product',
            'stores', 'categories'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $o_data = [];
        $errors = [];
        $o_data['redirect'] = route('products');
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'store_id' => 'required',
            'price' => 'required|numeric:2',
            'category_id' => 'required',
            'quantity' => 'required|numeric',
            'status' => 'required',
        ];
        if (!$request->filled('id')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occurred";
            $errors = $validator->messages();
        } else {
            $new_product = new Product();
            $new_product->fill($validator->validated());

            $check = Product::whereRaw('Lower(name) = ?', [strtolower($new_product->name)])
                ->where('store_id', '=', $new_product->store_id)
                ->where('id', '!=', $request->id)->get();
            if ($check->count() > 0) {
                $message = "Product with same name already exists";
                $errors['name'] = 'Product with same name already exists';
            } else {
                if ($request->id) {
                    DB::beginTransaction();
                    try {
                        $product = Product::find($request->id);
                        $product->fill($new_product->toArray());
                        $product->image = $request->has('image') ? $this->verifyAndStoreImage($request, 'image', 'products')
                            : $product->image;
                        $product->save();
                        DB::commit();
                        $status = "1";
                        $message = "Product updated Successfully";

                    } catch (EXCEPTION $e) {
                        DB::rollback();
                        $message = "Failed to update product " . $e->getMessage();
                    }
                } else {
                    DB::beginTransaction();
                    try {
                        $new_product->image = $this->verifyAndStoreImage($request, 'image', 'products');
                        $new_product->save();
                        DB::commit();
                        $status = "1";
                        $message = "Product Created Successfully";

                    } catch (EXCEPTION $e) {
                        DB::rollback();
                        $message = "Failed to create product " . $e->getMessage();
                    }
                }
            }
        }
        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function getProductList(Request $request)
    {

        $sqlBuilder = Product::join('malls', 'malls.mall_id', '=', 'products.store_id')
            ->join('product_categories as categories', 'categories.category_id', '=', 'products.category_id')
            ->select(['products.id', 'products.name', 'products.price',
                'products.quantity', 'products.status', 'products.image', 'products.created_at',
                'malls.mall_name as store_name', 'categories.category_name as category_name']);
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('image', function ($data) {
            return "<ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>
                <li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-xs pull-up' aria-label='Sophia Wilkerson'  data-bs-original-title='Sophia Wilkerson'>
                    <img class='rounded-circle' src='" . asset('storage/products/' . $data['image']) . "' style='width:50px; height:50px;'>
                </li>
            </ul>";
        });
        $dt->edit('price', function ($data) {
            return number_format($data['price']) . ' AED';
        });
        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });
        $dt->edit('status', function ($data) {
            $checked = ($data["status"] == 1) ? "checked" : "";
            $html = '<label class="switch s-icons s-outline  s-outline-warning  mb-4 mr-2">
                <input type="checkbox" data-role="active-switch"
                    data-href="' . route('products.status_change', ['id' => encrypt($data['id'])]) . '"
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
            if (get_user_permission('products', 'u')) {
                $html .= '<a class="dropdown-item"
                        href="' . route('products.edit', ['id' => encrypt($data['id'])]) . '"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
            }
            if (get_user_permission('products', 'd')) {
                $html .= '<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="' . route('products.delete', ['id' => encrypt($data['id'])]) . '"><i
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

        $item = Product::where(['id' => $id])->get();
        if ($item->count() > 0) {
            $item = $item->first();
            Product::where('id', '=', $id)->update(['status' => $request->status]);
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

        $category_data = Product::where(['id' => $id])->first();

        if ($category_data) {
            Product::where(['id' => $id])->delete();
            $message = "Product deleted successfully";
            $status = "1";
        } else {
            $message = "Fail to delete record.";
        }

        echo json_encode([
            'status' => $status, 'message' => $message
        ]);
    }

}
