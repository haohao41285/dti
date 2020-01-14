<?php

namespace App\Http\Controllers\ItTools;

use App\Models\PosPlace;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\PosMerchantPermission;
use App\Models\PosUser;
use App\Models\PosUserGroup;
use App\Models\PosMerchantPerUserGroup;
use DB;
use Hash; 

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
                return '<a class="btn btn-sm btn-secondary delete" place_id="'.$row->place_id.'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
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
            'customer_phone' => 'required|unique:pos_user,user_phone|digits:10',
        ];
        $message = [
            'customer_phone.digits' => 'Enter a valid phone, Please!'
        ];
        $validator = Validator::make($request->all(),$rule,$message);

        if($validator->fails())
            return response([
                'status' => 'error',
                'message' => $validator->getMessageBag()->toArray(),
            ]);

        //FORMAT USER PHONE
            $customer_phone = preg_replace("/[^0-9]/", "", $request->customer_phone );
            $start_phone = substr($customer_phone,0,1);

            if( $start_phone == '0' )
                $customer_phone = "1".substr($customer_phone,1);

            else $customer_phone = "1".$customer_phone;

        $check_exist_phone = PosUser::where('user_phone',$customer_phone)->count();
        if($check_exist_phone > 0)
            return response(['status'=>'error','message'=>'The customer phone has already been taken.']);

        //GET PERMISSON
        $permission_arr = [];

        $permissions = PosMerchantPermission::select('mp_id')->get();

        foreach($permissions as $permission){
            $permission_arr[] = $permission->mp_id;
        }
        $permission_list = implode(',',$permission_arr );

        //SET PLACE_ID
        $place_max = PosPlace::max('place_id')+1;
        // return $place_max;

        DB::beginTransaction();

        //CREATE PLACE IP LICENSE
            if(strlen($place_max) < 6){
                $place_ip_license = "DEG-".str_repeat("0",(6 - strlen($place_max))).$place_max;
            }else
                $place_ip_license = "DEG-".$place_max;

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
                'place_ip_license' => $place_ip_license,
                'place_status' => 1,
                'place_phone' => $request->customer_phone,
                'place_demo'  => 1,
                'place_actiondate' => '{"mon": {"start": "09:00", "end": "21:00", "closed": false}, "tue": {"start": "09:00", "end": "21:00", "closed": false}, "wed": {"start": "09:00", "end": "21:00", "closed": false}, "thur": {"start": "09:00", "end": "21:00", "closed": false}, "fri": {"start": "09:00", "end": "21:00", "closed": false}, "sat": {"start": "09:00", "end": "21:00", "closed": false},"sun": {"start": "09:00", "end": "21:00", "closed": false} }'
            ];
            $create_place = PosPlace::insert($place_arr);

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
                'user_phone' => $customer_phone,
                'user_demo' => 1
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

            if(!isset($create_place) || !isset($create_user) || !isset($create_role) || !isset($create_permission)){
                DB::callback();
                return response(['status' => 'error' ,'message' => 'Failed! Create Place Failed!']);
            }
            else{
                DB::commit();
                return response(['status' => 'success' ,'message' => 'Successfully! Create Place Successfully!']);
            }
    }
    public function delete(Request $request){

        $place_id = $request->place_id;

        DB::beginTransaction();

        $update_place = PosPlace::where('place_id',$place_id)->delete();
        $update_user = PosUser::where('user_place_id',$place_id)->delete();
        $update_role = PosUserGroup::where('ug_place_id',$place_id)->delete();
        $update_permision_per_role = PosMerchantPerUserGroup::where('mpug_place_id',$place_id)->delete();

        if(!isset($update_place) || !isset($update_user) || !isset($update_role) || !isset($update_permision_per_role)){
            DB::callback();
            return response(['status'=>'error','message'=>'Failed! Delete Place Failed!']);
        }
        else{
            DB::commit();
            return response(['status'=>'success','message'=>'Successfully! Delete Successfully!']);
        }
    }

}
