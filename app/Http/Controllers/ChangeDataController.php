<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainUser;
use App\Models\MainComboService;
use DB;

class ChangeDataController extends Controller
{
    public function transferUser(){

    	MainUser::truncate();
    	// $user_arr = [];

    	$user_list_new = DB::table('users')->get();
    	$phone = 988888110;

    	foreach ($user_list_new as $key => $user) {
    		$user_arr = [
    			'user_id' => $user->id,
    			'user_firstname' => $user->fullname,
    			'user_nickname' => $user->username,
    			'user_password' => $user->password,
    			'user_phone' => $user->cellphone==""?"0".$phone:$user->cellphone,
    			'user_status' => 1,
    			'user_country_code' => '84',
    			'user_email' => $user->email,
    			'user_group_id' => 1,
    			'user_team' => 1,
    			'user_token' => csrf_token(),
    		];
    		$phone++;
    	MainUser::create($user_arr);
    	}

    }
    public function transferService(){
    	MainComboService::truncate();

    	$combo_service_old = DB::table('services')->get();
    	
    	$service_arr = [];

    	foreach ($combo_service_old as $key => $service) {


    		switch ($service->service_form) {
    			case 'website':
    				$service_form = 2;
    				break;
    			case 'facebook_ads':
    				$service_form = 3;
    			case 'google_review':
    				$service_form = 1;
    			default:
    				$service_form = 4;
    				break;
    		}

    		$service_arr[] = [
    			'cs_name' => $service->name,
    			'cs_price' => $service->price,
    			'cs_expiry_period' => 6,
				'cs_description' => $service->desc,
				'cs_status' => 1,
				'cs_type' => 2,
				'cs_assign_to' => $service->manager_id,
				'cs_form_type' => $service_form,
				'cs_combo_service_type' => 1,
    		];
    	}
    	MainComboService::insert($service_arr);
    }
}
