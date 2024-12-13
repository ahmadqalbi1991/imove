<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyProblem;
use Illuminate\Http\Request;

class VehicleEmergencyController extends Controller
{
    public function index()
    {
        $page_heading = "Vehicle Problems";
        $problems = EmergencyProblem::get();
        return view('admin.vehicle_problems.list',compact('problems','page_heading'));
    }

    public function create()
    {
        $page_heading = "Vehicle Problems Create";
        return view('admin.vehicle_problems.create',compact('page_heading'));
    }

    public function edit($id)
    {
        $page_heading = "Vehicle Problems Edit";
        $problem = EmergencyProblem::find($id);
        return view('admin.vehicle_problems.create',compact('page_heading', 'problem'));
    }

    public function store(Request $request) {
        $message = 'Emergency problem has been saved';
        if ($request->has('id')) {
            $problem = EmergencyProblem::find($request->id);
        } else {
            $problem = new EmergencyProblem();
        }

        $problem->title = $request->title;
        $problem->description = $request->description;
        $problem->save();

        echo json_encode(['status' => "1", 'message' => $message]);
    }

    public function destroy(Request $request) {
        $message = 'Emergency problem has been deleted';
        $problem = EmergencyProblem::find($request->id);
        $problem->delete();
        echo json_encode(['status' => "1", 'message' => $message]);
    }
}
