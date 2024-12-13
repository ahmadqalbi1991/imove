<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manufacturer;

class ManufacturerController extends Controller
{
    public function index()
    {
        $page_heading = "Make";
        $make = Manufacturer::orderBy('id', 'desc')->get();

        return view('admin.manufacturer.list', compact('make', 'page_heading'));
    }

    public function create()
    {
        $page_heading = "Add Make";
        return view('admin.manufacturer.create', compact('page_heading'));
    }

    public function store(Request $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
        $redirectUrl = '';
        $file_name = '';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
//            $dir = config('global.upload_path') . "/" . config('global.manufacturer_images');
//            $file_name = time() . uniqid() . "." . $file->getClientOriginalExtension();
//            $file->move($dir, $file_name);
            $fileName = time() . '_' . $file->getClientOriginalName();
            $s3Path = 'imove/manufactures/' . $fileName;
            $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
            $file_name = \Storage::disk('s3')->url($s3Path);
        }


        Manufacturer::create([
            'name' => $request->name,
            'status' => $request->active,
            'logo' => $file_name,
        ]);
        $status = "1";
        $message = "Manufacturer added successfully";
        echo json_encode(['status' => $status, 'message' => $message, 'errors' => $errors]);
    }

    public function edit(Request $request, $id)
    {
        $page_heading = "Edit Make";
        $make = Manufacturer::whereId($id)->first();

        return view('admin.manufacturer.edit', compact('make', 'page_heading'));
    }

    public function update(Request $request, $id)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $s3Path = 'imove/manufactures/' . $fileName;
            $filePath = \Storage::disk('s3')->put($s3Path, file_get_contents($file));
            $file_name = \Storage::disk('s3')->url($s3Path);

        } else {
            $file_name = "";
        }
        Manufacturer::whereId($id)->update([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'logo' => $file_name,
            'status' => $request->active,
        ]);
        $status = "1";
        $message = "Manufacturer updated successfully";
        echo json_encode(['status' => $status, 'message' => $message]);
    }

    public function delete(Request $request, $id)
    {
        $item = Manufacturer::whereId($id)->first();
        $item->delete();
        $status = "1";
        $message = "Manufacturer deleted successfully";
        echo json_encode(['status' => $status, 'message' => $message]);
    }

    public function addVehicle()
    {
        $page_heading = "Add New Vehicle";
        return view('admin.vehicle.create', compact('page_heading'));
    }

    public function allVehicle()
    {
        $page_heading = "Vehicles";
        return view('admin.vehicle.list', compact('page_heading'));
    }

    public function inspectionRequest()
    {
        $page_heading = "Inspection Requests";
        return view('admin.request.list', compact('page_heading'));
    }

    public function qoutationList()
    {
        $page_heading = "Qoutation Requests";
        return view('admin.qoutation.list', compact('page_heading'));
    }
}
