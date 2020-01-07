<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainUser;
use App\Models\MainComboService;
use App\Models\MainCustomer;
use App\Models\MainCustomerTemplate;
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
    public function transferCustomer(){

    	MainCustomerTemplate::truncate();
    	// MainCustomer::truncate();

    	// $customer_arr = [];

    	$old_customers = DB::table('customers')->get();

    	foreach ($old_customers->chunk(1000) as $key => $customers) {
    		
    	$customer_template_arr = [];

    		foreach ($customers as $key => $customer) {
    			//ADD ALL CUSTOMER TEAMPLATE ARRAY
	    		$customer_template_arr[] = [
	    			'id' => $customer->id,
	    			'ct_salon_name' => $customer->business==""?'salon':$customer->business,
	    			'ct_fullname' => $customer->fullname, 
	    			'ct_firstname' => $customer->fullname,
					'ct_lastname' => "no",
					'ct_business_phone' => $customer->business_phone,
					'ct_cell_phone' => $customer->cell_phone,
					'ct_email' => $customer->email,
					'ct_address' => $customer->address,
					'ct_website' => $customer->website,
					'ct_note' => $customer->notes,
					'created_by' => $customer->created_by,
					'updated_by' => $customer->modified_by,
					'created_at' => $customer->created_date,
					'ct_active' => 1
	    		];
	    		//ADD CUSTOMER ARRAY
	    		/*if($customer->status_id == 2)

		    		$customer_arr[] = [
		    			'customer_id' => $customer->id,
						'customer_lastname' => 'on',
						'customer_firstname' => $customer->fullname,
						'customer_email' => $customer->email,
						'customer_phone' => $customer->cell_phone,
						'customer_address' => $customer->address,
						'customer_city' => $customer->city,
						'customer_zip' => $customer->zipcode,
						'customer_state' => $customer->state,
						'customer_status' => 1,
						'customer_customer_template_id' => $customer->id,
						'created_at' => $customer->created_date,
		    		];*/
    		}
    	MainCustomerTemplate::insert($customer_template_arr);
    	}
    	// MainCustomer::insert($customer_arr);
    }
}
