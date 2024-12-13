<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUsModel;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;

class ContactUsController extends Controller
{
    //
    public function index(){
        $page_heading = "Contact us";
        $mode="List";
        $datamain = ContactUsModel::get();
        
        return view('admin.contact_us.list',compact('mode','page_heading','datamain'));
    }

  

    
}
