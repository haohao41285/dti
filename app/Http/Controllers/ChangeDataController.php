<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainUser;
use App\Models\MainComboService;
use App\Models\MainCustomer;
use App\Models\MainCustomerTemplate;
use App\Models\MainUserCustomerPlace;
use App\Models\MainTeamType;
use App\Models\PosPlace;
use DB;
use App\Models\MainTeam;
use Auth;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
    public function transferCustomerStatus(){

    	MainUserCustomerPlace::truncate();

    	$old_customers = DB::table('customers')->get();
    	$customer_array = [];

    	foreach ($old_customers->chunk(1000) as $key => $customers) {

    		foreach ($customers as $key => $customer) {
    			if(!is_null($customer->assigned_by))

    				$customer_array[] = [
    					'user_id' => $customer->assigned_by,
						'team_id' => 1,
						'customer_id' => $customer->id,
						'created_at' => $customer->created_date
    				];
    		}
    	}
    	MainUserCustomerPlace::insert($customer_array);
    }
    public function transferCustomerTeamType(){

    	// MainTeamType::truncate();

    	$old_customers = DB::table('customers')->get();
    	$customer_array = [];

    	foreach ($old_customers->chunk(1000) as $key => $customers) {

    		foreach ($customers as $key => $customer) {

    			if($customer->status_id != 1){

    				switch ($customer->status_id) {
    					case 2:
    					    $status = 4;
    						break;
    					case 3:
    					    $status = 1;
    						break;
    					default:
    					    $status = 2;
    						break;
    				}

	    			$customer_array[$customer->id] = $status;
    			}
    		}
    	}
    	$customer_list_status = json_encode($customer_array);

    	DB::table('main_team_type')->update(['team_customer_status' => $customer_list_status]);
    }
    public function addCoumn(){
        Schema::table('main_customer_template', function($table) {
            $table->integer('test_column');
        });
    }
    public function removeCoumn(){
        if (Schema::hasColumn('main_customer_template', 'test_column'))
        {
            Schema::table('main_customer_template', function (Blueprint $table) {
                $table->dropColumn('test_column');
            });
        }else{
            Schema::table('main_customer_template', function($table) {
                $table->integer('test_column');
            });
        }
    }
    public function addSlug(){
        $team_types = DB::table('main_team_type')->get();
        // $arr = [];
        foreach ($team_types as $key => $value) {

            $slug = str_replace('-', '_', str_slug($value->team_type_name));
            DB::table('main_team_type')->where('id',$value->id)->update(['slug'=>$slug]);

            if (Schema::hasColumn('main_customer_template', $slug))
            {
                Schema::table('main_customer_template', function (Blueprint $table) use ($slug)  {
                    $table->dropColumn($slug);
                });
            }else{
                Schema::table('main_customer_template', function($table) use ($slug)  {
                    $table->integer($slug);
                });
            }
        }
        // return $arr;
    }
    public function addCustomerStatus(){
        //GET USER'S TEAM TYPE
        $team_slug = MainTeam::find(Auth::user()->user_team)->getTeamType->slug;

        $old_customers = DB::table('customers')->get();

        foreach ($old_customers->chunk(1000) as $key => $customers) {
            
            // $id_arr = [];
            // $status_arr = [];
            // $status_customer = 0;

            foreach ($customers as $key => $customer) {
                $id_arr[] = $customer->id;

                switch ($customer->status_id) {
                        case 2:
                            $status_customer = 4;
                            break;
                        case 3:
                            $status_customer = 1;
                            break;
                        case 1:
                            $status_customer = 3;
                            break;
                        default:
                            $status_customer = 2;
                            break;
                    }
                    DB::table('main_customer_template')->where('id',$customer->id)->update([$team_slug=>$status_customer]);
            }
        }
    }
    public function addCustomerToUser(){

        /*$customer_list = DB::table('customers')->get();
        $place_list = DB::table('pos_place')->select('id','place_phone')->get();
        $place_list = collect($place_list);

        foreach ($customer_list->chunk(1000) as $key => $customers) {

            foreach ($customers as $key => $customer) {

                if($customer->status_id == 2){

                }
            }
        }*/
        $user_places = MainUserCustomerPlace::with('getCustomer')->get();

        foreach ($user_places as $key => $user_place) {

            $customer_phone = $user_place->getCustomer->ct_business_phone;

            $place_info = PosPlace::where('place_phone',$customer_phone)->first();
            // return $place_info;

            if(isset($place_info)){
                MainUserCustomerPlace::where('customer_id',$user_place->customer_id)->update(['place_id'=>$place_info->place_id]);
            }
        }
        // return $customer_phone;
    }
    public function replaceCharacterSpace(){
        $places = DB::table('pos_place')->select('place_id','place_phone')->get();
        foreach ($places as $key => $place) {
            if(!is_null($place->place_phone)){
                $new_phone = str_replace('(','',str_replace(')','',str_replace(' ','',str_replace('-','',$place->place_phone))));
                // if($new_phone != 'AcrylicFullSet' && $new_phone != "Children'sBasic" && $new_phone != 'CLASSICMANICURE' && $new_phone != 'Acrylicfullset'){
                    $new_phone_arr[] = $new_phone;
                //     DB::table('pos_place')->where('place_id',$place->place_id)->update(['place_phone'=>$new_phone]);
                // }
                // else
                //     DB::table('pos_place')->where('place_id',$place->place_id)->update(['place_phone'=>'']);
            }
        }
        return $new_phone_arr;
    }
}
