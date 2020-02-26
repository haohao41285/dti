<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainUser;
use App\Models\PosPlace;
use App\Models\MainComboService;
use App\Models\MainCustomer;
use App\Models\MainCustomerTemplate;
use App\Models\MainCustomerService;
use App\Models\MainComboServiceBought;
use Carbon\Carbon;
use App\Models\MainTeam;
use Auth;
use DB;

class OldOrderController extends Controller
{
    public function index(){
    	
    	$data['user_list'] = MainUser::active()->orderBy('user_lastname','asc')->get();
    	$data['place_list'] = PosPlace::active()->orderBy('place_name','asc')->get();
    	$data['main_customer'] = MainCustomer::all();
    	$service_arr = MainComboService::active()->orderBy('cs_name','asc')->get()->toArray();

    	$alphabet_arr = range('A','Z');
    	$service_list = [];

    	foreach ($alphabet_arr as $key => $character) {
    		$position = 0;
    		foreach (array_slice($service_arr,$position) as $key_service => $service) {
    			if( substr(str_slug($service['cs_name']),0,1)  == str_slug($character) ){
    				$service_list[$character][] = $service;
    				$position = $key_service+1;
    			}
    		}
    	}

    	$data['service_list'] = $service_list;

    	return view('orders.old_order',$data);
    }
    public function searchBusiness(Request $request){
    	$place_id = $request->place_id;

    	$customer_info = PosPlace::where('place_id',$place_id)->first()->customer;
    	if(empty($customer_info) || $customer_info == null || $customer_info == "")
    		return response(['message'=>'empty']);

    	return response(['message'=>'notEmpty','customer_info' => $customer_info]);
    }
    public function searchCustomer(Request $request){

    	$customer_id = $request->customer_id;
    	$business_info = MainCustomer::where('customer_id',$customer_id)->first()->getPlaces;

    	if(count($business_info) == 0)
    		return response(['message'=>'empty']);

    	return response(['message'=>'notEmpty','business_info'=>$business_info]);

    }
    public function save(Request $request){

    	$created_by = $request->created_by;

    	if(!isset($request->cs_id) || count($request->cs_id) == 0 )
    		return back()->with(['error'=>'Choose Service!']);
    	if(!isset($request->place_id))
    		return back()->with(['error'=>'Choose Bussiness']);
    	if(!isset($request->customer_id))
    		return back()->with(['error'=>'Choose Customer']);

    	$service_list = implode(';', $request->cs_id);
    	DB::beginTransaction();

    	if($request->customer_id == 0){
    		//UPDATE CUSTOMER STATUS
			$team_info = MainTeam::find(MainUser::where('user_id',$created_by)->first()->user_team)->getTeamType;
		    $team_slug = $team_info->slug;

    		//CREATE MAIN CUSTOMER TEMPLATE
    		$ct_arr = [
    			'ct_firstname' => $request->customer_firstname,
    			'ct_lastname' => $request->customer_lastname,
    			'ct_phone' => $request->customer_phone,
    			'ct_email' => $request->customer_email,
    			'ct_address' => $request->customer_address,
    			 $team_slug => 4
    		];
    		$ct_update = MainCustomerTemplate::create($ct_arr);

    		$customer_id_template = $ct_update->id;

    		//CREATE MAINCUSTOMER
    		$max_customer_id = MainCustomer::max('customer_id')+1;
    		$customer_arr = [
    			'customer_id' => $max_customer_id,
    			'customer_firstname' => $request->customer_firstname,
    			'customer_lastname' => $request->customer_lastname,
    			'customer_phone' => $request->customer_phone,
    			'customer_email' => $request->customer_email,
    			'customer_address' => $request->customer_address,
    			'customer_customer_template_id' => $customer_id_template
    		];
    		$customer_update = MainCustomer::create($customer_arr);

    		$customer_id = $max_customer_id;
    	}else{
    		$customer_id = $request->customer_id;
    	}
    	//UPDATE BUSINESS
    	if($request->place_id == 0){
    		$max_place_id = PosPlace::where('place_customer_id',$customer_id)->max('place_id')+1;
    		$place_arr = [
    			'place_id' => $max_place_id,
    			'place_code' => 'place_code',
    			'place_logo' => 'logo',
    			'place_name' => $request->place_name??"salon",
    			'place_website' => 'place_website',
    			'place_taxcode' => 'place_taxcode',
    			'place_actiondate_option' => 1,
    			'place_customer_type' => 1,
    			'place_url_plugin' => 'url',
    			'created_by' => $created_by,
    			'updated_by' => $created_by,
    			'place_ip_license' => 'DEG_'.$max_place_id,
    			'place_phone' => $request->place_phone,
    			'place_address' => $request->place_address??"",
    			'place_email' => $request->place_email??"",
    		];
    		$place_id = $max_place_id;
    	}else{
    		$place_id = $request->place_id;
    	}

    	$date_order = format_datetime_db($request->date_order);
    	//UPDATE ORDER
		$order_arr = [
			'csb_customer_id' => $customer_id,
			'csb_combo_service_id' => $service_list,
			'csb_trans_id' => 111111,
			'csb_place_id' => $place_id,
			'csb_amount' => $request->csb_amount,
			'csb_charge' => $request->csb_charge,
			'csb_amount_deal' => $request->csb_amount_deal,
			'created_by' => $created_by,
			'csb_status' => 4,
			'csb_cashback' => 0,
			'created_at' => $date_order,
			'updated_by' => Auth::user()->user_id,

		];
		$order_update = MainComboServiceBought::create($order_arr);

		//UPDATE MAIN SERVICE CUSTOMER
		
		if($request->customer_id == 0){
			$service_list = MainComboService::whereIn('id',$request->cs_id)->get();
			foreach ($service_list as $key => $service) {
				if($service->cs_type_time == 1){
					$cs_date_expire = Carbon::parse($request->date_order)->addMonths($service->cs_expiry_period)->format('Y-d-m');
				}else{
					$cs_date_expire = Carbon::parse($request->date_order)->addDays($service->cs_expiry_period)->format('Y-d-m');
				}
				$cs_arr = [
					'cs_place_id' => $place_id,
					'cs_customer_id' => $customer_id,
					'cs_service_id' => $service->id,
					'cs_date_expire' => $cs_date_expire,
					'cs_type' => 0,
					'created_by' => $created_by,
					'updated_by' => $created_by,
				];
				$service_customer = MainCustomerService::create($cs_arr);
			}
		}
		if(!$order_update){
			DB::callback();
			return back()->with(['error'=>'Failed! Save Order Failed!']);
		}else{
			DB::commit();
			return back()->with(['success'=> 'Save Order Successfully!']);
		}
    }
}
