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
use App\ModelsCustomer;
use App\Models\MainCustomerAssign;
use App\Models\MainComboServiceBought;
use App\Models\MainTask;

class ChangeDataController extends Controller
{
    public function transferUser(){

    	MainUser::truncate();
    	// $user_arr = [];

    	$user_list_new = DB::table('users')->get();
    	$phone = 988888110;

    	foreach ($user_list_new as $key => $user) {

            //GET NAME
            $fullname = explode(' ',$user->fullname);
            $fullname = array_filter($fullname);
            $count = count($fullname);
            $first_name = $fullname[$count-1];
            $last_name = "";
            for ($i=0; $i < $count-1 ; $i++) {
                $last_name .= $fullname[$i]." ";
            }


    		$user_arr = [
    			'user_id' => $user->id,
    			'user_firstname' => $first_name,
                'user_lastname' => $last_name,
    			'user_nickname' => $user->username,
    			'user_password' => $user->password,
    			'user_phone' => $user->cellphone==""?"0".$phone:$user->cellphone,
    			'user_status' => 1,
    			'user_country_code' => '84',
    			'user_email' => $user->email,
    			'user_group_id' => 1,
    			'user_team' => 2,
    			'user_token' => csrf_token(),
    		];
    		$phone++;

    	MainUser::insert($user_arr);
    	}
        // return $user_arr;

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
                'id' => $service->id,
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

    			if(!is_null($customer->seller_id) && $customer->status_id == 2)

    				$customer_array[] = [
    					'user_id' => $customer->seller_id,
						'team_id' => 2,
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
    public function tranferCustomerId(){
        $customer_list = DB::table('main_customer_template')->get();

        foreach ($customer_list->chunk(10000) as $key => $customers) {
            foreach ($customers as $key => $customer) {
                MainCustomerTemplate::where('id',$customer->id)->update(['old_customer_id'=>$customer->id]);
            }
            sleep(2);
        }
    }
    public function checkCustomer(){

        $old_customer = DB::table('main_customer')->get();
        $old_cutomer = collect($old_customer);
        $new_customer = DB::table('main_customer_new')->get();
        $new_customer = collect($new_customer);

        $customer_arr = [];

        foreach ($new_customer as $key => $customer) {
            $check_phone = $old_customer->where('customer_phone',$customer->customer_phone)->count();

            if( $check_phone > 0 )
                $customer_arr[] = [
                    'id' => $customer->id,
                    'customer_phone' => $customer->customer_phone
                ];

        }
        return $customer_arr;


    }
    public function mergeCustomer(){
        
        MainCustomerTemplate::truncate();

        DB::beginTransaction();

        ModelsCustomer::chunk(1000,function($customers){

            foreach ($customers as $key => $customer) {

                    if (strpos($customer->fullname, '.') !== false) {
                        $customer_fullname = explode('.', $customer->fullname);
                    }else{
                        $customer_fullname = explode(' ', $customer->fullname);
                    }

                    $firstname = $customer_fullname[1]??'vi';
                    $lastname = $customer_fullname[0]??'quy';

                if($customer->status_id == 2){
                    $business_phone = '1'.$customer->business_phone;
                    //CHECK MAIN_CUSTOMER EXISTED
                    $main_customer_info = MainCustomer::where('customer_phone',$business_phone);
                    if($main_customer_info->count() > 0){
                        $main_customer_info->update(['customer_customer_template_id'=>$customer->id]);

                    }else{
                        //CREATE MAIN_CUSTOMER
                        $main_customer_arr = [
                            'customer_lastname' => $lastname,
                            'customer_firstname' => $firstname,
                            'customer_email' => $customer->email,
                            'customer_phone' => $business_phone,
                            'customer_address' => $customer->address,
                            'customer_city' => $customer->city,
                            'customer_zip' => $customer->zipcode,
                            'customer_state' => $customer->state,
                            'customer_type' => 2,
                            'customer_status' => 1,
                            'customer_customer_template_id' => $customer->id,
                            'created_at' => $customer->created_date,
                        ];
                        $customer_update = MainCustomer::create($main_customer_arr);
                        //CREATE POS_PLACE
                        $max_place_id = PosPlace::max('place_id')+1;

                        if(strlen($max_place_id) < 6){
                            $place_ip_license = "DEG-".str_repeat("0",(6 - strlen($max_place_id))).$max_place_id;
                        }else
                            $place_ip_license = "DEG-".$max_place_id;

                        $place_arr = [
                            'place_id' => $max_place_id,
                            'place_customer_id' => MainCustomer::max('customer_id'),
                            'place_code' => 'place-'.$max_place_id,
                            'place_logo' => 'logo',
                            'place_name' => $customer->business??"salon",
                            'place_website' => 'place_website',
                            'place_taxcode' => 'place_taxcode',
                            'place_actiondate_option' => 0,
                            'place_customer_type' => 1,
                            'place_url_plugin' => 'url',
                            'created_by' => 1,
                            'updated_by' => 1,
                            'place_ip_license' => $place_ip_license,
                            'place_phone' => $customer->business_phone,
                            'place_address' => $customer->address??"",
                            'place_email' => $customer->email??"",
                            'place_actiondate' => '{"mon": {"start": "09:00", "end": "21:00", "closed": false}, "tue": {"start": "09:00", "end": "21:00", "closed": false}, "wed": {"start": "09:00", "end": "21:00", "closed": false}, "thur": {"start": "09:00", "end": "21:00", "closed": false}, "fri": {"start": "09:00", "end": "21:00", "closed": false}, "sat": {"start": "09:00", "end": "21:00", "closed": false},"sun": {"start": "09:00", "end": "21:00", "closed": false} }',
                            'place_status' => 1
                        ];
                        PosPlace::insert($place_arr);
                    }
                }
                //CREATE MAIN_CUSTOMER_TEMPLATE
                $customer_template_arr = [
                    'id' => $customer->id,
                    'ct_salon_name' => $customer->business??"salon",
                    'ct_fullname' => $customer->fullname??"quy vi",
                    'ct_firstname' => $firstname,
                    'ct_lastname' => $lastname,
                    'ct_business_phone' => $customer->cell_phone,
                    'ct_cell_phone' => $customer->business_phone,
                    'ct_email' => $customer->email,
                    'ct_address' => $customer->address,
                    'ct_website' => $customer->website,
                    'ct_note' => $customer->notes,
                    'created_by' => $customer->created_by,
                    'updated_by' => $customer->modified_by,
                    'created_at' => $customer->created_date,
                    'ct_active' => 1,
                ];
                $customer_template_update = MainCustomerTemplate::insert($customer_template_arr);
            }
            if(!$customer_template_update){
                DB::rollBack();
                return 'error';
            }else{
                DB::commit();
                return 'done';
            }
        });
       
    }
    public function setDisabledCustomer(){
        $team_type = MainTeamType::select('slug')->get();
        $slug_update = [];
        foreach ($team_type as $key => $slug) {
            $slug_update[$slug->slug] = 2;
        }
        $customers = ModelsCustomer::where('status_id',4)->get();
        foreach ($customers as $key => $customer) {
            DB::table('main_customer_template')->where('id',$customer->id)->update($slug_update);
        }
    }
    function setAssignedCustomer(){

        // DB::beginTransaction();

        MainUserCustomerPlace::truncate();
        MainCustomerAssign::truncate();

        $customers = ModelsCustomer::join('main_user',function($join){
            $join->on('csr_customers.seller_id','main_user.user_id');
        })->where('status_id',3)->get();
        // return $customers->count();
        // $count = 0;
        foreach ($customers as $key => $customer) {
            //GET TEAM TYPE && SLUG TEAM TYPE
            $team_slug = MainTeam::find($customer->user_team)->getTeamType->slug;
            //UPDATE MAIN_CUSTOMER_TEMPLATE STATUS WITH TEAM TYPE
             $customer_template_update = DB::table('main_customer_template')->where('id',$customer->id)->update([$team_slug=>1]);
             // return $customer->id . "- " .$team_slug;

            //UPDATE MAIN USER CUSTOMER PLACE
            $user_customer_place_arr = [
                'user_id' => $customer->user_id,
                'team_id' => $customer->user_team,
                'customer_id' => $customer->id,
                'created_at' => $customer->modified_date,
            ];
            $customer_user_update = MainUserCustomerPlace::insert($user_customer_place_arr);

            //UPDATE MAIN CUSTOMER ASSIGN
            $assigned_customer = [
                'customer_id' => $customer->id,
                'business_phone' => $customer->business_phone,
                'business_name' => $customer->business??"salon",
                'user_id' => $customer->user_id,
                'address' => $customer->address,
                'email' => $customer->email,
                'website' => $customer->website,
            ];
            $assign_customer_update = MainCustomerAssign::insert($assigned_customer);

            // if(!$customer_template_update || !$customer_user_update || !$assign_customer_update){

            // }else{
            //     $count ++;
            // }

        }
        return 'done';
        // return $count;
        // if($count  == 2715){
        //     DB::commit();
        //     return 'ok';
        // }else{
        //     DB::rollBack();
        //     return "not ok";
        // }
    }
    function setServicedCustomer(){

        DB::beginTransaction();
        $customers = ModelsCustomer::join('main_customer',function($join){
            $join->on('csr_customers.id','main_customer.customer_customer_template_id');
        })
        ->join('main_user',function($join){
            $join->on('csr_customers.seller_id','main_user.user_id');
        })
        ->where('status_id',2)->get();
        // $count = 0;
        foreach ($customers as $key => $customer) {
            //UPDATE MAIN CUSTOMER TEMPLATE STATUS WITH TEAM TYPE
            $team_slug = MainTeam::find($customer->user_team)->getTeamType->slug;
            $customer_template_update = DB::table('main_customer_template')->where('id',$customer->id)->update([$team_slug=>4]);
            // return $customer->customer_id;

            //GET PLACE ID WITH MAIN CUSTOMER ID
            $place_info = PosPlace::where('place_customer_id',$customer->customer_id)->first();
            // $place_id = $place_info->place_id;

            ///UPDATE MAIN USER CUSTOMER PLACE
            $user_customer_place_arr = [
                'user_id' => $customer->user_id,
                'team_id' => $customer->user_team,
                'customer_id' => $customer->id,
                'created_at' => $customer->modified_date,
                'place_id' => $place_info->place_id??"",
            ];
            $customer_user_update = MainUserCustomerPlace::insert($user_customer_place_arr);

            // if(!$customer_user_update){}
            // else{
            //     $count ++;
            // }
        }
        // if($count == 865){
        //     DB::commit();
        //     return 'ok';
        // }else{
        //     DB::rollBack();
        //     return 'not ok';
        // }
        return 'done';
    }
    public function userOrder(){

        DB::beginTransaction();
        MainComboServiceBought::truncate();
        MainTask::truncate();

        $orders = DB::table('orders')->join('main_customer',function($join){
            $join->on('orders.customer_id','main_customer.customer_customer_template_id');
        })
        // ->where('status','DONE')
        ->select('orders.customer_id as order_customer','orders.*','main_customer.*')
        ->get();
        // return $orders;
        // return $orders->count();
        // $count = 0;
        $order_id =1;
        foreach ($orders as $key => $order) {
            //GET AUTHNET INFO
            if( !empty($order->authnet))
                $authnet_info = unserialize($order->authnet);
            //GET PLACE ORDER
            $place_info = PosPlace::where('place_customer_id',$order->customer_id)->first();

            //GET SOMBO SERVICE ORDER
            $service_list = DB::table('order_detail')->join('services',function($join){
                $join->on('order_detail.service_id','services.id');
            })
            ->where('order_id',$order->id)->select('order_detail.service_id','services.*')->get();
            // return $service_list;
            $service_arr = [];
            foreach ($service_list as $service) {
                $service_arr[] = $service->service_id;
                //CREATE TASK
                $task_arr = [
                    'subject' => $service->name,
                    'priority' => 2,
                    'status' => 3,
                    'complete_percent' => 100,
                    'assign_to' => $service->manager_id,
                    'order_id' => $order_id,
                    'created_by' => $order->created_by,
                    'updated_by' => $order->created_by,
                    'created_at' => $order->created_date,
                    'updated_at' => $order->created_date,
                    'service_id' => $service->service_id,
                    'category' => 1,
                    'place_id' => $place_info->place_id??""
                ];
                MainTask::insert($task_arr);
            }
            $service_id = implode(';', $service_arr);


            $order_arr = [
                'csb_customer_id' => $order->customer_id,
                'csb_place_id' => $place_info->place_id??"",
                'csb_combo_service_id' => $service_id,
                'csb_amount' => $order->subtotal,
                'csb_charge' => $order->amount,
                'csb_cashback' => 0,
                'csb_payment_method' => 2,
                'csb_card_number' => isset($authnet_info['card_num'])?substr($authnet_info['card_num'],-5):"",
                'csb_trans_id' => $authnet_info['trans_id']??"",
                'created_at' => $order->created_date,
                'updated_at' => $order->created_date,
                'csb_note' => $order->note,
                'csb_status' => 4,
                'created_by' => $order->created_by
            ];
            MainComboServiceBought::insert($order_arr);
            // $count++;
            $order_id ++;
        }
        // if($count == 413){
        //     DB::commit();
        //     return 'ok';
        // }else{
        //     DB::rollBack();
        //     return 'not ok';
        // }
        return 'done';
    }
}
