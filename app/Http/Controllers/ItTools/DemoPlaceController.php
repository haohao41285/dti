<?php

namespace App\Http\Controllers\ItTools;

use App\Models\PosPlace;
use Dotenv\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DemoPlaceController extends Controller
{
    public function index(){
        return view('tools.demo-place');
    }
    public function datatable(Request $request){
        $demo_places = PosPlace::where('place_status',1)->where('place_demo',1);
        return DataTables::of($demo_places)
            ->editColumn('place_demo',function($row){
                if($row->place_demo  == 1)
                    $check = 'checked';
                else $check = "";
                return '<input type="checkbox" place_demo="'.$row->place_demo.'" place_id="'.$row->place_id.'" class="js-switch"'.$check.'/>';
            })
            ->addColumn('action',function($row){
                return '<a class="btn btn-sm btn-secondary view" href="javascript:void(0)"><i class="fas fa-eye"></i></a>
                        <a class="btn btn-sm btn-secondary delete" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
            })
            ->rawColumns(['action','place_demo'])
            ->make('true');
    }
    public function changeDemoStatus(Request $request){

        $place_demo = $request->place_demo;
        $place_id = $request->place_id;
        if($place_demo == 1)
            $place_demo_update = 0;
        else
            $place_demo_update = 1;
        $update_place = PosPlace::where('place_id',$place_id)->update(['place_demo'=>$place_demo_update]);

        if(!isset($update_place))
            return response(['status'=>'error','message'=>'Failed! Change Demo Status Failed!']);
        else
            return response(['status'=>'success','message'=>'Successfully! Change Demo Status Successfully!']);
    }
    public function save(Request $request){
        $rule = [
            'customer_phone' => 'required|unique:main_customer,customer_phone|unique:main_customer_template,ct_business_phone|unique:main_customer_template,ct_cell_phone|unique:pos_place,place_phone|digits_between:10,15',
            'place_phone' => 'required|required|unique:main_customer,customer_phone|unique:main_customer_template,ct_business_phone|unique:main_customer_template,ct_cell_phone|unique:pos_place,place_phone|digits_between:10,15'
        ];
        $validator = Validator::make($request->all(),$rule);
        if($validator->fails())
            return response([
                'status' => 'error',
                'message' => $validator->getMessageBag()->toArray(),
            ]);
        $customer_arr = [
            'customer_firstname' => $request->customer_firstname,
            'customer_lastname' => $request->customer_lastname,
            'customer_phone' => $request->customer_phone,
            'customer_email' => 'demo_customer@gmail.com',
            'customer_address' => 'demo address',
            'customer_zip' => '111',
            'customer_state' => 'demo state',
            'customer_city' => 'demo city',

        ];
    }
    public function save(Request $request){

        //GET PERMISSON
        $permission_arr = [];

        $permissions = PosMerchantPermission::select('mp_id')->get();

        foreach($permissions as $permission){
            $permission_arr[] = $permission->mp_id;
        }
        $permission_list = implode(',',$permission_arr );

        //SET PLACE_ID
        $place_max = PosPlace::max('place_id')+1;

            //CREATE PLACE
            $place_arr = [
                'place_id' => $place_max,
                'place_code'=> 'place_'.$place_max,
                'place_logo' => 'logo',
                'place_name' => 'Nail_'.$place_max,
                'place_address' => 'address_'.$place_max,
                'place_website' => 'website_'.$place_max,
                'place_taxcode' => '1234'.$place_max,
                'place_customer_type' => 1,
                'place_url_plugin' => "place_url_plugin",
                'place_ip_license' => '1234567'.$place_max,
                'place_status' => 1,
                'place_phone' => $request->place_phone
            ];
            $create_place = PosPlace::create($place_arr);

            //CREATE USER
            $user_arr = [
                'user_id' => 1,
                'user_place_id' => $place_max,
                'user_default_place_id' => $place_max,
                'user_usergroup_id' => 1,
                'user_password' => Hash::make('abc123'),
                'user_fullname' => 'user_'.$place_max,
                'user_token' => $request->_token,
                'remember_token' => $request->_token,
                'user_status' => 1,
                'user_phone' => $request->customer_phone,
            ];
            $create_user = PosUser::create($user_arr);

            //CREATE ROLE
            $role_arr = [
                'ug_id' => 1,
                'ug_place_id' => $place_max,
                'ug_name' => 'admin',
                'ug_role' => '',
                'ug_merchant_role' => '',
                'ug_status' => 1
            ];
            $create_role = PosUserGroup::create($role_arr);

            //CREATE PERMISSION
            $permission_admin = [
                'mp_id' => $permission_list,
                'ug_id' => 1,
                'mpug_place_id' => $place_max
            ];
            $create_permission = PosMerchantPerUserGroup::create($permission_admin);
            if(!isset($create_place) || !isset($create_user) || !isset($create_role) || !isset($create_permission))
                return response(['status' => 'error' ,'message' => 'Failed! Create Place Failed!']);
            else
                return response(['status' => 'success' ,'message' => 'Successfully! Create Place Successfully!']);

    }

}
