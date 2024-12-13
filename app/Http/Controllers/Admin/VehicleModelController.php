<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleModel;
use App\Models\Manufacturer;
use App\Models\VehicleType;
use Validator;

class VehicleModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_heading = "Vehicle Models";
        $mdels = VehicleModel::get();
        return view('admin.vehicle_model.list',compact('mdels','page_heading'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_heading = "Create Vehicle Models";
        $manufacturer = Manufacturer::get();
        $types = VehicleType::get();
        return view('admin.vehicle_model.create',compact('manufacturer','page_heading','types'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function store(Request $request)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
        $redirectUrl = '';
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'manufacturer' => 'required',
        ]);
        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $check = VehicleModel::where('id','!=',$request->id)->where('model',$request->name)->get()->count();
            if($check == 0)
            {
                $datains = new VehicleModel;
                if($request->id)
                {
                $datains = VehicleModel::find($request->id);   
                }
                $datains->model = $request->name;
                $datains->model_ar = $request->name_ar;
                $datains->manufacturer_id = $request->manufacturer;
                $datains->type_id = $request->type_id;
                $datains->status = $request->active;
                $datains->save();
                
                $status = "1";
                $message = "Vehicle modal saved successfully";
            }
            else{
                $status = "0";
                $message = "Vehicle modal already exist";  
                $errors['name'] = $request->name . " already added";
            }
            
        }
        echo json_encode(['status' => $status, 'message' => $message, 'errors' => $errors]);
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_heading = "Update Vehicle Models";
        $model = VehicleModel::whereId($id)->first();
        $manufacturer = Manufacturer::get();
        $types = VehicleType::get();
        return view('admin.vehicle_model.create',compact('page_heading','model','manufacturer','types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'manufacturer' => 'required',
        ]);
        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
        VehicleModel::whereId($id)->update([
            'model'=>$request->name,
            'manufacturer_id' => $request->manufacturer,
            'status' => $request->active
        ]);

        $status = "1";
        $message = "Vehicle Modal saved successfully";
       }

        echo json_encode(['status' => $status, 'message' => $message, 'errors' => $errors]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = "0";
        $message = "";
        $o_data = [];

        $model = VehicleModel::whereId($id)->first();
        $model->delete();

        $status = "1";
        $message = "Deleted successfully";

        echo json_encode(['status' => $status, 'message' => $message, 'o_data' => $o_data]);
    }
}
