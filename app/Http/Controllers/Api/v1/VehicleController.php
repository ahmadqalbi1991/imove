<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\EmergencyProblem;
use App\Models\Manufacturer;
use App\Models\User;
use App\Models\UserVehicle;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function listVehicles(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $vehicles = UserVehicle::where('user_id', $user->id)
                ->orderBy('vehicle_name', 'ASC')->get()->toArray();

            $oData['vehicles'] = convertNumbersToStrings($vehicles);

            return return_response('1', 200, 'List fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function saveVehicle(Request $request)
    {
        try {
            $rules = [
                'access_token' => 'required',
                'vehicle_name' => 'required',
                'category_id' => 'required',
                'model_year' => 'required',
                'manufacturer_id' => 'required',
                'model_id' => 'required'
            ];


            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $vehicle_name = $request->vehicle_name;
            $user_id = $user->id;
            $category_id = $request->category_id;
            $manufacturer_id = $request->manufacturer_id;
            $model_id = $request->model_id;
            $model_year = $request->model_year;
            DB::beginTransaction();
            if ($request->has('id')) {
                $vehicle = UserVehicle::find($request->id);
                $vehicle->vehicle_name = $vehicle_name;
                $vehicle->user_id = $user_id;
                $vehicle->category_id = $category_id;
                $vehicle->manufacturer_id = $manufacturer_id;
                $vehicle->model_id = $model_id;
                $vehicle->model_year = $model_year;
                $vehicle->save();

                DB::commit();
                $status = "1";
                $message = "Vehicle updated successfully";
            } else {
                $vehicle = new UserVehicle();
                $vehicle->vehicle_name = $vehicle_name;
                $vehicle->user_id = $user_id;
                $vehicle->category_id = $category_id;
                $vehicle->manufacturer_id = $manufacturer_id;
                $vehicle->model_id = $model_id;
                $vehicle->model_year = $model_year;
                $vehicle->save();

                DB::commit();
                $vehicle = UserVehicle::find($vehicle->id);
                $status = "1";
                $message = "Vehicle added successfully";
            }

            $oData['vehicle'] = convertNumbersToStrings($vehicle->toArray());

            return return_response($status, 200, $message, [], $oData);
        } catch (\Exception $exception) {
            DB::rollback();
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function deleteVehicle(Request $request) {
        try {
            $rules = [
                'access_token' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $vehicle = UserVehicle::find($request->id);
            if (empty($vehicle)) {
                return return_response('0', 200, 'Vehicle not found');
            }

            $vehicle->delete();
            return return_response('1', 200, 'Vehicle deleted');
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getVehicle(Request $request) {
        try {
            $rules = [
                'access_token' => 'required',
                'id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $vehicle = UserVehicle::where('id', $request->id)->first();

            if (empty($vehicle)) {
                return return_response('0', 200, 'Vehicle not found');
            }

            $oData['vehicle'] = convertNumbersToStrings($vehicle->toArray());
            return return_response('1', 200, 'Vehicle fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getTypes(Request $request) {
        try {
            $types = VehicleType::get();
            $oData['types'] = convertNumbersToStrings($types->toArray());

            return return_response('1', 200, 'Types fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getManufacturers(Request $request) {
        try {
            $manufacturers = Manufacturer::where('status', 1)->get();
            $oData['manufacturers'] = convertNumbersToStrings($manufacturers->toArray());

            return return_response('1', 200, 'Manufacturers fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getModels(Request $request) {
        try {
            $rules = [
                'manufacturer_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $models = VehicleModel::where('manufacturer_id', $request->manufacturer_id)->where('status', 1)->get();
            $oData['models'] = convertNumbersToStrings($models->toArray());

            return return_response('1', 200, 'Models fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getProblems(Request $request) {
        try {
            $problems = EmergencyProblem::get();
            $oData['problems'] = convertNumbersToStrings($problems->toArray());

            return return_response('1', 200, 'Problems fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }
}
