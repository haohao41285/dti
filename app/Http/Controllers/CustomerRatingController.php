<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainComboServiceBought;
use App\Models\MainComboService;
use App\Models\MainCustomerRating;
use DB;

class CustomerRatingController extends Controller
{
    public function index($token){

    	$data['order_info'] = MainComboServiceBought::where('csb_token',$token)->first();
    	if(!$data['order_info'])
    		$data['error'] = 'error';

    	else{
    		$data['place_info'] = $data['order_info']->getPlace;
	    	$combo_service_arr = explode(';',$data['order_info']->csb_combo_service_id);
	    	$data['combo_service_list'] = MainComboService::whereIn('id',$combo_service_arr)->get();
	    	$data['token'] = $token;
    	}
    	return view('customer_rating',$data);
    }
    public function postRating(Request $request){
    	//CHECK ORDER
    	$order_info = MainComboServiceBought::where('csb_token',$request->order_token)->first();
    	if(!$order_info)
    		return response(['status'=>'error','message'=>'Failed! Send Rating Failed!']);

    	$rating_arr = [
    		'note' => $request->note,
    		'order_id' => $order_info->id,
    		'rating_level' => $request->rating_level,
            'service' => $request->service,
            'continue_buy' => $request->continue_buy,
            'introduce' => $request->introduce
    	];

    	DB::beginTransaction();

    	$rating_update = MainCustomerRating::create($rating_arr);
    	$order_update = $order_info->update(['csb_token'=>'']);

    	if(!$rating_update || !$order_update){
    		DB::callback();
    		return response(['status'=>'error','message'=>'Failed! Send Rating Failed!']);
    	}else{
    		DB::commit();
    		return response(['status'=>'success','message'=>'Successfully!']);
    	}
    }
}
