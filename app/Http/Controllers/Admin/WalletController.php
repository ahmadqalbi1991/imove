<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Wallet;
use Validator;
use Illuminate\Support\Facades\Auth;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class WalletController extends Controller
{
    //

    public function index(){
        $page_heading = "Wallet";
        $mode="List";
        return view('admin.wallet.list',compact('mode', 'page_heading'));
    }

    public function getwalletList(Request $request){

        $sqlBuilder = Wallet::rightJoin('users','users.id','=','user_wallets.user_id')->select([
            DB::raw('name::text as name'),
            DB::raw('email::text as email'),
            DB::raw('users.id::text as user_id'),
            DB::raw('user_wallets.amount::text as amount'),
            DB::raw('user_wallets.created_at::text as created_at'),
            DB::raw('user_wallets.id::text as id')
        ])->where('role_id',3);

        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);

        $dt->edit('created_at',function($data){
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });

        
        $dt->edit('amount',function($data){
            return number_format($data['amount'],2);
        });



        $dt->add('action', function($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
                    if(get_user_permission('wallet','u')){
                    $html.='<a class="dropdown-item"
                        href="'.route('wallet.edit',['id'=>encrypt($data['user_id'])]).'"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
                    }
                    
                    if(get_user_permission('wallet','u')){
                        $html.='<a class="dropdown-item"
                            href="'.route('wallet.add',['id'=>encrypt($data['user_id'])]).'"><i
                                class="flaticon-plus"></i> Add</a>';
                        }
                                                   
                    

            $html.='</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }


    public function edit($id){
        $id = decrypt($id);

        $user = User::find($id);
        $name = $user->name;
        $email = $user->email;

        if(!empty($user->wallet)){
            $amount = $user->wallet->amount;
        }else{
            $amount = 0;
        }


        $page_heading = 'Wallet Edit';
        $mode = "Detail";
        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');

        
        return view('admin.wallet.edit',compact('id','mode', 'page_heading','operations','site_modules','email','name','amount'));

    }


    public function add($id){
        $id = decrypt($id);

        $user = User::find($id);
        $name = $user->name;
        $email = $user->email;

        if(!empty($user->wallet)){
            $amount = $user->wallet->amount;
        }else{
            $amount = 0;
        }

        $page_heading = 'Wallet Add';
        $mode = "Detail";
        $site_modules = config('crud.site_modules');
        $operations   = config('crud.operations');

        
        return view('admin.wallet.add',compact('id','mode', 'page_heading','operations','site_modules','email','name','amount'));

    }



    public function update(REQUEST $request){
        
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('wallet.list');
       
            $amount  = $request->amount;
            
            $id  = $request->id;
                    $rules = [
                        'amount' => 'required',
                    ];
            
                    $validator = Validator::make($request->all(),$rules);
            
                    if ($validator->fails()) {
                        $status = "0";
                        $message = "Validation error occured";
                        $errors = $validator->messages();
                    }
                    else {

                        $value = [
                            'amount'  => $request->amount,
                        ];

                        Wallet::updateOrCreate(['user_id' => $request->id],
                        ['amount' => $request->amount]);
                        
                        $status = "1";
                        $message = "User Wallet Updated Successfully";
                    }
        

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function updateamt(REQUEST $request){
        
        $status     = "0";
        $message    = "";
        $o_data     = [];
        $errors     = [];
        $o_data['redirect'] = route('wallet.list');
       
            $amount  = $request->amount;
            $add_amount  = $request->add_amount;

            $toal = ($amount + $add_amount);
            
            $id  = $request->id;
                    $rules = [
                        'add_amount' => 'required',
                    ];
            
                    $validator = Validator::make($request->all(),$rules);
            
                    if ($validator->fails()) {
                        $status = "0";
                        $message = "Validation error occured";
                        $errors = $validator->messages();
                    }
                    else {

                        Wallet::updateOrCreate(['user_id' => $request->id],
                        ['amount' => $toal]);
                        
                        $auth_id = Auth::user()->id;

                        $data = [
                            'user_wallet_id'  => $request->id,
                            'amount'  => $request->add_amount,
                            'type'  => 'credit',
                            'created_by'  => $auth_id,
                            'created_at' => Carbon::now(),
                        ];
                        DB::table('user_wallet_transactions')->insert($data);
                        

                        $status = "1";
                        $message = "User Wallet Updated Successfully";
                    }
        

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }


}
