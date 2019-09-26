<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Option;
use App\Helpers\GeneralHelper;
use App\Helpers\ImagesHelper;
use App\Models\MainCustomerTemplate;
use App\Models\MainComboService;
use App\Models\MainComboServiceBought;
use App\Models\MainCustomerService;
use App\Models\MainTeam;
use App\Models\PosPlace;
use App\Models\MainCustomer;
use App\Models\PosUser;
use App\Models\MainUser;
use App\Models\MainTrackingHistory;
use App\Models\MainTask;
use App\Models\MainFile;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Carbon\Carbon;
use DataTables;
use Validator;
use Auth;
use DB;
use Hash;
use ZipArchive;

class OrdersController extends Controller 
{
	/**
	 * get all orders
	 * return
	 */
	public function index(){
		$data['state'] = Option::state();
        $data['status'] = GeneralHelper::getOrdersStatus();
		return view('orders.orders',$data);
	}

	public function getMyOrders(){
		$data['state'] = Option::state();
        $data['status'] = GeneralHelper::getOrdersStatus();
		return view('orders.my-orders',$data);
	}

	public function getSellers(){
		$data['state'] = Option::state();
        $data['status'] = GeneralHelper::getOrdersStatus();
        $team = Auth::user()->user_team;
        $data['user_teams'] = MainUser::where('user_team',$team)->get();
        $data['services'] = MainComboService::where('cs_status',1)->get();
		return view('orders.sellers',$data);
	}

	public function add($customer_id = 0){

        if($customer_id != 0 ){
        }

        $data['customer_info'] = MainCustomerTemplate::where('id',$customer_id)->first();

        if(!empty($data['customer_info'])){
        	$data['place_list'] = PosPlace::join('main_customer',function($join){
			    	$join->on('pos_place.place_customer_id','main_customer.customer_id');
			    })
			    	->where('main_customer.customer_phone',$data['customer_info']->ct_business_phone)
					->select('pos_place.place_id','pos_place.place_name')
			        ->get();
        }
       
        $data['combo_service_list'] = MainComboService::where('cs_status',1)->orderBy('cs_type','asc')->get();

        $data['count'] = round($data['combo_service_list']->count()/2);

        return view('orders.add',$data);
	}
	function authorizeCreditCard(Request $request)
	{
		if($request->credit_card_type != 'E-CHECK'){
			$rule = [
				'payment_amount' => 'required',
				'credit_card_type' => 'required',
				'credit_card_number' => 'required',
				'experation_month' => 'required',
				'experation_year' => 'required',
				'cvv_number' => 'required',
				'first_name' => 'required',
				'last_name' => 'required',
				'cs_id' =>'required',
				'customer_phone' => 'required',
				'customer_id' =>'required',
				'place_id' => 'required'
			];
			$message = [
				'payment_amount.required' => 'Enter Amount',
				'credit_card_type.required' => 'Choose Credit card Type',
				'credit_card_number.required' => 'Enter Card Number',
				'experation_month.required' => 'Choose Experation Date',
				'experation_year.required' => 'Choose Experation Date',
				'cvv_number.required' => 'Enter svv number',
				'cs_id.required' => 'Choose Combo Service',
				'customer_phone.required' => 'Enter Customer Phone',
				'customer_id.required' => 'Choose Customer',
				'place_id.required' => 'Choose Place'
			];
		}else{
			$rule = [
				'payment_amount' => 'required',
				'credit_card_type' => 'required',
				'routing_number' => 'required',
				'account_number' => 'required',
				'bank_name' => 'required',
				'first_name' => 'required',
				'last_name' => 'required',
				'cs_id' =>'required',
				'customer_phone' => 'required',
				'customer_id' =>'required',
				'place_id' => 'required'
			];
			$message = [
				'payment_amount.required' => 'Enter Amount',
				'credit_card_type.required' => 'Choose Credit card Type',
				'routing_number.required' => 'Enter Routing Number',
				'account_number.required' => 'Choose Account Number',
				'bank_name.required' => 'Choose Bank Name',
				'cs_id.required' => 'Choose Combo Service',
				'customer_phone.required' => 'Enter Customer Phone',
				'customer_id.required' => 'Choose Customer',
				'place_id.required' => 'Choose Place'
			];
		}
		$validator = Validator::make($request->all(),$rule,$message);
		if($validator->fails())
			// return back()->with('error' => $validator->getMessageBag()->toArray());
			return redirect()->back()->withErrors($validator)->withInput();

		DB::beginTransaction();

			//GET CUSTOMER INFORMATION
			$customer_phone = $request->customer_phone;

			$customer_info = MainCustomerTemplate::where(function($query) use ($customer_phone){
									$query->where('ct_business_phone',$customer_phone)
										->orWhere('ct_cell_phone',$customer_phone);
									})
									->where('ct_active',1)
									->first();

			//CHECK CUSTOMER IN MAIN_CUSTOMER
			$check_customer = MainCustomer::where('customer_phone',$customer_phone)->first();

			if(isset($check_customer))
				$customer_id = $check_customer->customer_id;

			else{

				$customer_id = MainCustomer::max('customer_id')+1;
				$main_customer_arr = [
					'customer_id' => $customer_id,
					'customer_lastname' => $customer_info->ct_lastname,
					'customer_firstname' => $customer_info->ct_firstname,
					'customer_email' => $customer_info->ct_email,
					'customer_address' => $customer_info->ct_address,
					'customer_phone' => $customer_info->ct_business_phone,
					'customer_city' => "",
					'customer_zip' => "",
					'customer_state' => 1,
				];
				MainCustomer::create($main_customer_arr);
			}

			$service_arr = [];
			$number_credit = "";
			$account_number = "";

			if($request->credit_card_number != "")
			    $number_credit =  substr($request->credit_card_number, 0,4)."####".substr($request->credit_card_number, -4);
			if($request->account_number != "")
			    $account_number =  substr($request->account_number, 0,4)."####".substr($request->account_number, -4);

			$combo_service_list = implode(";",$request->cs_id);
			$today = Carbon::today();

			//CHECK SERVICE OR COMBO
			foreach ($request->cs_id as $key => $value) {
				$service_list = MainComboService::where('id',$value)->first();
				if($service_list->cs_type == 1)
					$service_arr = array_merge(explode(";",$service_list->cs_service_id),$service_arr);
				else
					$service_arr[] = $value;
			}

			if($request->place_id != 0){
				$place_id = $request->place_id;
			}else{
				$place_id = PosPlace::max('place_id')+1;
			}
			$cs_id = MainCustomerService::where('cs_place_id',$place_id)->max('cs_id')+1;

			//UPDATE MAIN_CUSTOMER_SERVICE
			foreach ($service_arr as $key => $service) {
				//GET EXPIRY PERIOD OF SERVICE
				$service_expiry_period = MainComboService::where('id',$service)->first()->cs_expiry_period;
				//CHECK CUSTOMER SERVICE EXIST 
				$check = MainCustomerService::where('cs_place_id',$place_id)
											->where('cs_customer_id',$customer_id)
											->where('cs_service_id',$service)
											->first();
				if(isset($check))
				{
					$cs_date_expire = $check->cs_date_expire;
					if($cs_date_expire >= $today){
						$cs_date_expire = Carbon::parse($cs_date_expire)->addMonths($service_expiry_period)->format('Y-m-d');
					}else
					    $cs_date_expire = Carbon::parse($today)->addMonths($service_expiry_period)->format('Y-m-d');

					//UPDATE SERVICE IN MAIN CUSTOMER SERVICE
					$customer_service_update = MainCustomerService::where('cs_place_id',$place_id)
											->where('cs_service_id',$service)
											->update(['cs_date_expire'=>$cs_date_expire,'updated_by'=>Auth::user()->user_id]);
				}else{
					$cs_date_expire = Carbon::parse($today)->addMonths($service_expiry_period)->format('Y-m-d');

					$order_arr = [
						'cs_id' => $cs_id,
						'cs_place_id' => $place_id,
						'cs_customer_id' => $customer_id,
						'cs_service_id' => $service,
						'cs_date_expire' => $cs_date_expire,
						'cs_type' => 0,
						'created_at' => Carbon::now(),
						'created_by' => Auth::user()->user_id,
						'cs_status' => 1,
					];
					$customer_service_update = MainCustomerService::insert($order_arr);
					$cs_id++;
				}
				
			}
			//END UPDATE MAIN_CUSTOMER_SERVICE

			//INSERT MAIN_COMBO_SERVICE_BOUGHT
			$order_history_arr = [
				'csb_customer_id' => $customer_id,
				'csb_combo_service_id' => $combo_service_list,
				'csb_amount' => $request->service_price_hidden,
				'csb_charge' => $request->payment_amount_hidden,
				'csb_cashback' => 0,
				'csb_payment_method' => 3,
				'csb_card_type' => $request->credit_card_type,
				'csb_amount_deal' => $request->discount,
				'csb_card_number' => $number_credit,
				'routing_number' => $request->routing_number,
				'account_number' => $account_number,
				'bank_name' => $request->bank_name,
				'csb_status' => $request->credit_card_type != 'E-CHECK'?1:0,
				'created_by' => Auth::user()->user_id,
			];
			//CREATE NEW PLACE IN POS_PLACE, NEW USER IN POS_USER IF CHOOSE NEW PLACE
			if($request->place_id == 0){
				//INSERT POS_PLACE
				$place_arr = [
					'place_id' => $place_id,
					'place_customer_id' => $customer_id,
					'place_code' => 'place-'.$place_id,
					'place_logo' => 'logo',
					'place_name' => 'New Name',
					'place_address' => $customer_info->ct_address,
					'place_website' => $customer_info->ct_website,
					'place_phone' => $customer_info->ct_business_phone,
					'place_taxcode' => 'tax-code',
					'place_customer_type' => 'customer type',
					'place_url_plugin' => 'url plugin',
					'created_by' => Auth::user()->user_id,
					'updated_by' => Auth::user()->user_id,
					'place_ip_license' => md5('place-'.$place_id.$customer_id),
					'place_status' => 1
				];
				// return $place_arr;
				PosPlace::insert($place_arr);

				//INSERT POS_USER
				//FORMAT PHONE NUMBER
				$phone = preg_replace("/[^0-9]/", "", $customer_info->ct_business_phone );
	            $start_phone = substr($phone,0,1);
	            if( $start_phone == '0' )
	            {
	                $phone = "1".substr($phone,1);
	            }
	            else
	            {
	                $phone = "1".$phone;
	            }

				$user_arr = [
					'user_id' => 1,
					'user_place_id' => $place_id,
					'user_default_place_id' => 0,
					'user_usergroup_id' => 1,
					'user_password' => Hash::make('abc123'),
					'user_pin' => 123456,
					'user_fullname' => $customer_info->ct_firstname.";".$customer_info->ct_lastname,
					'user_phone' => $phone,
					'user_email' => $customer_info->ct_email,
					'user_token' => csrf_token(),
					'remember_token' => csrf_token(),
					'created_by' => Auth::user()->user_id,
					'updated_by' => Auth::user()->user_id,
					'enable_status' => 1,
					'user_status' => 1
				];
				PosUser::create($user_arr);
			}

			//UPDATE CUSTOMER STATUS
			$team_customer_status = MainTeam::where('id',Auth::user()->user_team)->first()->team_customer_status;

			if($team_customer_status == ""){

			    $team_customer_status_arr[][$request->customer_id] = 4;

			}else{
				$team_customer_status_arr = json_decode($team_customer_status,TRUE);
				$team_customer_status_arr[$request->customer_id] = 4;
			}
		    $team_customer_status_list = json_encode($team_customer_status_arr);

			$update_team_customr_status = MainTeam::where('id',Auth::user()->user_team)->update(['team_customer_status'=>$team_customer_status_list]);

		if($request->credit_card_type != 'E-CHECK'){
			/* Create a merchantAuthenticationType object with authentication details
		       retrieved from the constants file */
		    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		    $merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
		    $merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));
		    
		    // Set the transaction's refId
		    $refId = 'ref' . time();
		    $experation_date = $request->experation_year."-".$request->experation_month;

		    // Create the payment data for a credit card
		    $creditCard = new AnetAPI\CreditCardType();
		    $creditCard->setCardNumber($request->credit_card_number); //"4111111111111111"
		    $creditCard->setExpirationDate($experation_date); //"2038-12"
		    $creditCard->setCardCode($request->cvv_number); // "123"
		    // Add the payment data to a paymentType object
		    $paymentOne = new AnetAPI\PaymentType();
		    $paymentOne->setCreditCard($creditCard);
		    // Create order information
		    $order = new AnetAPI\OrderType();
		    $order->setInvoiceNumber("10101");
		    $order->setDescription($request->note); //"Golf Shirts"
		    // Set the customer's Bill To address
		    $customerAddress = new AnetAPI\CustomerAddressType();
		    $customerAddress->setFirstName($request->first_name);    //"Ellen"
		    $customerAddress->setLastName($request->last_name);    //"Johnson"
		    $customerAddress->setCompany("");
		    $customerAddress->setAddress($request->address);    //"14 Main Street"
		    $customerAddress->setCity($request->city);    //"Pecan Springs"
		    $customerAddress->setState($request->state);    //"TX"
		    $customerAddress->setZip($request->zip_code);    //"44628"
		    $customerAddress->setCountry($request->country);   //"USA"
		    // Set the customer's identifying information
		    $customerData = new AnetAPI\CustomerDataType();
		    $customerData->setType("individual");
		    $customerData->setId("");
		    $customerData->setEmail("");
		    // Add values for transaction settings
		    $duplicateWindowSetting = new AnetAPI\SettingType();
		    $duplicateWindowSetting->setSettingName("duplicateWindow");
		    $duplicateWindowSetting->setSettingValue("60");
		    // Add some merchant defined fields. These fields won't be stored with the transaction,
		    // but will be echoed back in the response.
		    $merchantDefinedField1 = new AnetAPI\UserFieldType();
		    $merchantDefinedField1->setName("customerLoyaltyNum");
		    $merchantDefinedField1->setValue("1128836273");
		    $merchantDefinedField2 = new AnetAPI\UserFieldType();
		    $merchantDefinedField2->setName("favoriteColor");
		    $merchantDefinedField2->setValue("blue");
		    // Create a TransactionRequestType object and add the previous objects to it
		    $transactionRequestType = new AnetAPI\TransactionRequestType();
		    $transactionRequestType->setTransactionType("authOnlyTransaction"); 
		    $transactionRequestType->setAmount($request->payment_amount);
		    $transactionRequestType->setOrder($order);
		    $transactionRequestType->setPayment($paymentOne);
		    $transactionRequestType->setBillTo($customerAddress);
		    $transactionRequestType->setCustomer($customerData);
		    $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
		    $transactionRequestType->addToUserFields($merchantDefinedField1);
		    $transactionRequestType->addToUserFields($merchantDefinedField2);
		    // Assemble the complete transaction request
		    $request = new AnetAPI\CreateTransactionRequest();
		    $request->setMerchantAuthentication($merchantAuthentication);
		    $request->setRefId($refId);
		    $request->setTransactionRequest($transactionRequestType);
		    // Create the controller and get the response
		    $controller = new AnetController\CreateTransactionController($request);
		    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
		    if ($response != null) {
		        // Check to see if the API request was successfully received and acted upon
		        if ($response->getMessages()->getResultCode() == "Ok") {
		            // Since the API request was successful, look for a transaction response
		            // and parse it to display the results of authorizing the card
		            $tresponse = $response->getTransactionResponse();
		        
		            if ($tresponse != null && $tresponse->getMessages() != null) {
		            	if(!isset($update_team_customr_status) || !isset($customer_service_update) ){
							DB::callback();
							return back()->with(['error'=>'Transaction Failed. Check again!']);
						}
						else{

							$order_history_arr['csb_trans_id'] =  $tresponse->getTransId();
							// return $order_history_arr;
							//INSERT NEW ORDER
							$insert_order = MainComboServiceBought::create($order_history_arr);

							//INSER MAIN_TASK
							$service_arr = array_unique($service_arr);
							$task_arr = [];
							foreach ($service_arr as $key => $service) {
								$service_name = MainComboService::where('id',$service)->first()->cs_name;
								$task_arr[] = [
									'subject' => $service_name,
									'priority' => 2,
									'status' => 1,
									'order_id'=> $insert_order->id,
									'created_by' => Auth::user()->user_id,
									'updated_by' => Auth::user()->user_id,
									'service_id' => $service,
								];
							}
							$task_create = MainTask::insert($task_arr);

							if(!isset($insert_order) 
								|| !isset($update_team_customr_status) 
								|| !isset($customer_service_update)
								|| !isset($task_create) ){

								return back()->with(['error'=>'Transaction Failed. Check again!']);
							}else{
								DB::commit();
							    return redirect()->route('my-orders')->with(['success'=>'Transaction Successfully!']);
							}
						}
		            	// return back()->with(['success'=>'Transaction Successfully!']);


		                // echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
		                // echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
		                // echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
		                // echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
		                // echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
		            } else {
		            	return back()->with(['error'=>'Transaction Failed. Check again!']);
		                // echo "Transaction Failed \n";
		                // if ($tresponse->getErrors() != null) {
		                //     // echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
		                //     // echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
		                // }
		            }
		            // Or, print errors if the API request wasn't successful
		        } else {
		        	return back()->with(['error'=>'Transaction Failed. Check again!']);

		            // echo "Transaction Failed \n";
		            // $tresponse = $response->getTransactionResponse();
		        
		            // if ($tresponse != null && $tresponse->getErrors() != null) {
		            //     echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
		            //     echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
		            // } else {
		            //     echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
		            //     echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
		            // }
		        }      
		    } else {
		        // echo  "No response returned \n";
				DB::callback();
		        return back()->with(['error'=>'No response returned. Check again!']);
		    }
		}
		else{
			//INSERT NEW ORDER
			$insert_order = MainComboServiceBought::create($order_history_arr);

			//INSER MAIN_TASK
			$service_arr = array_unique($service_arr);
			$task_arr = [];
			foreach ($service_arr as $key => $service) {
				$service_name = MainComboService::where('id',$service)->first()->cs_name;
				$task_arr[] = [
					'subject' => $service_name,
					'priority' => 2,
					'status' => 1,
					'order_id'=> $insert_order->id,
					'created_by' => Auth::user()->user_id,
					'updated_by' => Auth::user()->user_id,
					'service_id' => $service,
					'category' => 'ORDER'
				];
			}
			$task_create = MainTask::insert($task_arr);

			if(!isset($insert_order) 
				|| !isset($update_team_customr_status) 
				|| !isset($customer_service_update)
				|| !isset($task_create) ){

				DB::callback();
				return back()->with(['error'=>'Transaction Failed. Check again!']);
			}else{
				DB::commit();
			    return redirect()->route('my-orders')->with(['success'=>'Transaction Successfully!']);
			}
		}
			
	}
	public function getCustomerInfor(Request$request)
	{
		$customer_phone = $request->customer_phone;

		$customer_list = Auth::user()->user_customer_list;

		if($customer_list == "")
			return response(['status'=>'error','message'=>'You dont have any customer']);

		$customer_arr = explode(";",$customer_list);

		$customer_info = MainCustomerTemplate::whereIn('id',$customer_arr)
								->where(function($query) use ($customer_phone){
									$query->where('ct_business_phone',$customer_phone)
									->orWhere('ct_cell_phone',$customer_phone);
								})
								->where('ct_active',1)
								->first();

		//CHECK CUSTOMER EXIST POS_USER
		$check_customer = MainCustomer::where(function($query) use ($customer_phone){
										$query->where('customer_phone',$customer_phone)
										->orWhere('customer_phone',$customer_phone);
									})
										->where('customer_status',1)
										->select('customer_id')
									    ->first();

	    $place_list = PosPlace::join('main_customer',function($join){
	    	$join->on('pos_place.place_customer_id','main_customer.customer_id');
	    })
	    	->where('main_customer.customer_phone',$customer_phone)
			->select('pos_place.place_id','pos_place.place_name')
	        ->get();

		if(!isset($customer_info) || !isset($place_list))
			return response(['status'=>'error','message'=>'Get Customer Error']);
		else{
			if($customer_info == "")
				return response(['status'=>'error','message'=>'Get Customer Error']);
			else
				return response(['customer_info'=>$customer_info,'place_list'=>$place_list]);
		}
	}
	public function myOrderDatatable(Request $request)
	{
		$start_date =$request->start_date;
		$end_date = $request->end_date;
		$my_order_arr = [];

		$my_order_list = MainComboServiceBought::join('main_customer',function($join){
						$join->on('main_combo_service_bought.csb_customer_id','main_customer.customer_id');
					})
						->join('main_user',function($join){
							$join->on('main_combo_service_bought.created_by','main_user.user_id');
						});

		if(isset($request->my_order)){
			$my_order_list = $my_order_list->where('main_combo_service_bought.created_by',Auth::user()->user_id);
		}
		if($start_date != ""){
			$start_date = Carbon::parse($request->start_date)->format('Y-m-d');
			$my_order_list = $my_order_list->whereDate('main_combo_service_bought.created_at','>=',$start_date);
		}
		if($end_date != ""){
			$end_date = Carbon::parse($request->end_date)->format('Y-m-d');
			$my_order_list = $my_order_list->whereDate('main_combo_service_bought.created_at','<=',$end_date);
		}

		$my_order_list = $my_order_list->select('main_combo_service_bought.*','main_customer.customer_lastname','main_customer.customer_firstname','main_user.user_nickname')
		->get();

	    foreach ($my_order_list as $key => $order) {

	    	//GET INFORMATION CARD
	    	if($order->csb_status==1)
	    	    $infor = "<span>ID: ".$order->csb_trans_id."</span><br><span>Name: ".$order->csb_card_type."</span><br><span>Number: ".$order->csb_card_number."</span>";
	    	else
	    		$infor = "<span>Account Number: ".$order->account_number."</span><br><span>Name: ".$order->routing_number."</span><br><span>Bank Name: ".$order->bank_name."</span>";

	    	$services = explode(";",$order->csb_combo_service_id);

	    	$service_list = MainComboService::whereIn('id',$services)->select('cs_name')->get();
	    	$service_name = "";
	    	foreach ($service_list as $service) {
	    		$service_name .= "-".$service->cs_name."<br>";
	    	}

	    	if(!isset($request->my_order))
	    		$order_date = "<a href='".route('order-view',$order->id)."'>".Carbon::parse($order->created_at)->format('m/d/Y H:i:s')." by ".$order->user_nickname."</a>";
	    	else
	    		$order_date = "<a href='".route('order-view',$order->id)."'>".Carbon::parse($order->created_at)->format('m/d/Y H:i:s')."</a>";

	    	//GET CUSTOMER INFORMATION
	    	$customer = "<span>Customer: ".$order->customer_firstname. " " .$order->customer_lastname."</span><br><span>Business Phone: ".$order->customer_phone."</span><br><span>Address: ".$order->customer_address."</span><br><span>Email: ".$order->customer_email."</span>";
	    	
	    	$my_order_arr[] = [
	    		'id' => $order->id,
	    		'order_date' => $order_date,
	    		'customer' => $customer,
	    		'servivce' => $service_name,
	    		'subtotal' => $order->csb_amount,
	    		'discount' => $order->csb_amount_deal,
	    		'total_charge' => $order->csb_charge,
	    		'status' => $order->csb_status==0?"NOTPAYMEET":"PAID",
	    		'information' => $infor,
	    	];
	    }
		return  DataTables::of($my_order_arr)
		        ->rawColumns(['servivce','information','customer','order_date'])
				->make(true);
	}
	public function sellerOrderDatatable(Request $request)
	{
		$start_date = $request->start_date;
		$end_date = $request->end_date;
		$service_id = $request->service_id;
		$user_id = $request->user_id;
		$team_id = Auth::user()->user_team;
		$order_arr = [];

		$order_list = MainComboServiceBought::join('main_user',function($join){
			$join->on('main_combo_service_bought.created_by','main_user.user_id');
		})
		    ->where('main_user.user_team',$team_id);

		if($start_date != ""){
			$start_date = Carbon::parse($start_date)->format('Y-m-d');
			$order_list = $order_list->whereDate('main_combo_service_bought.created_at','>=',$start_date);
		}
		if($end_date != ""){
			$end_date = Carbon::parse($end_date)->format('Y-m-d');
			$order_list = $order_list->whereDate('main_combo_service_bought.created_at','<=',$end_date);
		}
		if($user_id != ""){
			$order_list = $order_list->where('main_combo_service_bought.created_by',$user_id);
		}
		if($service_id != ""){
			$order_list = $order_list->where(function($query) use ($service_id){
				$query->where('csb_combo_service_id',$service_id)
				->orWhere('csb_combo_service_id','like','%;'.$service_id)
				->orWhere('csb_combo_service_id','like',$service_id.";%")
				->orWhere('csb_combo_service_id','like','%;'.$service_id.';%');
			});
		}
		$order_list = $order_list->select('main_combo_service_bought.*','main_user.user_nickname')->get();

		foreach ($order_list as $key => $order) {

	    	$infor = "<span>ID: ".$order->csb_trans_id."</span><br><span>Name: ".$order->csb_card_type."</span><br><span>Number: ".$order->csb_card_number."</span>";

	    	$services = explode(";",$order->csb_combo_service_id);

	    	$service_list = MainComboService::whereIn('id',$services)->select('cs_name')->get();
	    	$service_name = "";
	    	foreach ($service_list as $service) {
	    		$service_name .= "-".$service->cs_name."<br>";
	    	}

	    	$order_arr[] = [
	    		'id' => $order->id,
	    		'order_date' => Carbon::parse($order->created_at)->format('m/d/Y H:i:s'),
	    		'customer' => $order->customer_firstname. " " .$order->customer_lastname,
	    		'servivce' => $service_name,
	    		'subtotal' => $order->csb_amount,
	    		'discount' => $order->csb_amount_deal,
	    		'total_charge' => $order->csb_charge,
	    		'seller' => $order->user_nickname,
	    		'information' => $infor,
	    	];
	    }
		return  DataTables::of($order_arr)
		        ->rawColumns(['servivce','information'])
				->make(true);
	}
	public function orderView($id)
	{
		$data['id'] = $id;
		$data['order_info'] = MainComboServiceBought::join('main_customer',function($join){
			$join->on('main_combo_service_bought.csb_customer_id','main_customer.customer_id');
		})
		->join('main_user',function($join){
			$join->on('main_combo_service_bought.created_by','main_user.user_id');
		})
		->where('main_combo_service_bought.id',$id)
		->select('main_combo_service_bought.*','main_user.user_nickname','main_user.user_email','main_customer.customer_phone','main_customer.customer_lastname','main_customer.customer_firstname')
		->first();

		$combo_service_list = $data['order_info']->csb_combo_service_id;
		$combo_service_arr = explode(";", $combo_service_list);
		$service_arr = [];

		//GET TASK LIST
		$data['task_list'] = MainTask::leftjoin('main_user',function($join){
			$join->on('main_task.updated_by','main_user.user_id');
		})
		->where('main_task.order_id',$id)
		->where(function($query){
			$query->where('main_task.created_by',Auth::user()->user_id)
			->orWhere('main_task.assign_to',Auth::user()->user_id)
			->orWhere('main_task.updated_by',Auth::user()->user_id);
		})
		->select('main_task.*','main_user.user_nickname')
		->get();
		
		return view('orders.order-view',$data);
	}
	public function orderTracking(Request $request){

		$order_id = $request->order_id;

		$order_tracking = MainUser::join('main_tracking_history',function($join){
			$join->on('main_tracking_history.created_by','main_user.user_id');
		})
			->where('main_tracking_history.order_id',$order_id)
			->whereNull('main_tracking_history.subtask_id')
			->select('main_tracking_history.*','main_user.user_firstname','main_user.user_lastname','main_user.user_team','main_user.user_nickname')->get();

		return DataTables::of($order_tracking)

			->addColumn('user_info',function($row){
				return '<span>'.$row->user_nickname.'('.$row->getFullname().')</span><br>
		                <span>'.Carbon::parse($row->created_at)->format('m/d/Y h:i A').'</span><br>
		                <span class="badge badge-secondary">'.$row->getTeam->team_name.'</span>';
			})
			->addColumn('task',function($row){
				return "<a href='' >Task#".$row->task_id."</a>";
			})
			->editColumn('content',function($row){
				$file_list = MainFile::where('tracking_id',$row->id)->get();
				$file_name = "<div class='row '>";
				if($file_list->count() > 0 ){

					foreach ($file_list as $key => $file) {
						$zip = new ZipArchive();

						if ($zip->open($file->name, ZipArchive::CREATE) !== TRUE) {
							$file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><img class="file-comment ml-2" src="'.asset($file->name).'"/></form>';
						}else{
							$file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>'.$file->name_origin.'</a></form>';
						}
					}
				}
				$file_name .= "</div>";
				return $row->content."<br>".$file_name;
			})
			->rawColumns(['user_info','task','content'])
			->make(true);
	}
	public function orderService(Request $request){

		$order_id = $request->order_id;

		$service_list = MainTask::join('main_combo_service',function($join){
			$join->on('main_task.service_id','main_combo_service.id');
		})
		->where('main_task.order_id',$order_id)
		->select('main_combo_service.*','main_task.id','main_combo_service.id as csb_id','main_task.note','main_task.content');

		return DataTables::of($service_list)
			->addColumn('action',function($row){
				return '<button type="button" form_type_id="'.$row->cs_form_type.'" task_id="'.$row->id.'" class="btn btn-sm btn-secondary input-form">INPUT FORM</button>';
			})
			->addColumn('infor',function($row){

				//GET FILES
				$file_name = "<div class='row'>";
				$file_list = $row->getFiles;
				foreach ($file_list as $key => $file) {
					$zip = new ZipArchive();

				    if ($zip->open($file->name, ZipArchive::CREATE) !== TRUE) {
						$file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><img class="file-comment ml-2" src="'.asset($file->name).'"/></form>';
					}else{
						$file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>'.$file->name_origin.'</a></form>';
					}

				}
				$file_name .= "</div>";

				$content = '';

				if($row->content != NULL){
					$content_arr = json_decode($row->content,TRUE);

					if($row->cs_form_type == 1){
						$content = '<span>Google Link: <b>'.$content_arr['google_link'].'</b></span><br>
	                    <span>Tên thợ nails: '.$content_arr['worker_name'].'</span><br>
	                    <div class="row">
	                        <span class="col-md-6">Number of starts: <b>'.$content_arr['star'].'</b></span>
	                        <span class="col-md-6">Số review hiện tại: <b>'.$content_arr['current_review'].'</b></span>
	                        <span class="col-md-6">Conplete date: <b>'.$content_arr['complete_date'].'</b></span>
	                        <span class="col-md-6">Số review yêu cầu: <b>'.$content_arr['order_review'].'</b></span>
	                    </div>
	                    <span>Note: <b>'.$row->note.'</span>
	                    <a href="javascript:void(0)">'.$file_name.'</a>';
					}
					if($row->cs_form_type == 2){
						$content = '<span>Tên sản phẩm: <b>'.$content_arr['product_name'].'</b></span><br>
	                    <span>Màu chủ đạo: <b>'.$content_arr['main_color'].'</b></span><br>
	                    <span>Thể loại hoặc phong cách khách hàng: <b>'.$content_arr['style_customer'].'</b></span><br>
	                    <span>Facebook Link: <b>'.$content_arr['link'].'</b></span><br>
	                    <span>Website: <b>'.$content_arr['website'].'</b></span><br>
	                    <span>Note: <b>'.$row->note.'</span>
	                    <a href="javascript:void(0)">'.$file_name.'</a>';
					}
					if($row->cs_form_type == 3){
						if(isset($content_arr['admin']) ) $admin = "YES";
						else $admin = "NO";
						if(isset($content_arr['image']) ) $image = "YES";
						else $image = "NO";
						$content = '<span>Facebook Link: <b>'.$content_arr['link'].'</b></span><br>
	                    <span>Promotion: <b>'.$content_arr['promotion'].'</b></span><br>
	                    <span>Số lượng bài viết: <b>'.$content_arr['number'].'</b></span><br>
	                    <div class="row">
	                        <span class="col-md-6">Đã có admin chưa: <b>'.$admin.'</b></span>
	                        <span class="col-md-6">Username: <b>'.$content_arr['user'].'</b></span>
	                        <span class="col-md-6">Có lấy được hình ảnh: <b>'.$image.'</b></span>
	                        <span class="col-md-6">Password: <b>'.$content_arr['password'].'</b></span>
	                    </div>
	                    <span>Note: <b>'.$row->note.'</span>
	                    <a href="javascript:void(0)">'.$file_name.'</a>';
					}
					if($row->cs_form_type == 4){

						if(isset($content_arr['show_price']) ) $show_price = "YES";
						else $show_price = "NO";

						$content = '<span>Domain: <b>'.$content_arr['domain'].'</b></span><br>
	                    <div class="row">
	                        <span class="col-md-6">Theme: <b>'.$content_arr['theme'].'</b></span>
	                        <span class="col-md-6">Show Price: <b>'.$show_price.'</b></span>
	                        <span class="col-md-6">Business Name: <b>'.$content_arr['business_name'].'</b></span>
	                        <span class="col-md-6">Business Phone: <b>'.$content_arr['business_phone'].'</b></span>
	                        <span class="col-md-6">Email: <b>'.$content_arr['email'].'</b></span>
	                        <span class="col-md-6">Address: <b>'.$content_arr['address'].'</b></span>
	                    </div>
	                    <span>Note: <b>'.$row->note.'</span>
	                    <a href="javascript:void(0)">'.$file_name.'</a>';
					}
				}
				return $content;
			})
			->rawColumns(['action','infor'])
			->make(true);
	}
	public function submitInfoTask(Request $request){
		// return $request->all();

		$input = $request->all();
		$current_month = Carbon::now()->format('m');
		$tracking_arr = [];

		unset($input['list_file']);
		unset($input['_token']);
		unset($input['list_file']);
		unset($input['task_id']);
		unset($input['note']);

		$content = json_encode($input);

		DB::beginTransaction();
		//ADD TRACKING HISTORY
    	$tracking_arr = [
    		'order_id' => $request->order_id,
    		'task_id' => $request->task_id,
    		'desription' => $request->note,
    		'created_by' => Auth::user()->user_id,
    	];
    	$tracking_create = MainTrackingHistory::create($tracking_arr);

    	//UPDATE TASK
    	$task_update = MainTask::where('id',$request->task_id)->update(['content'=>$content,'note'=>$request->note]);

        //DELETE OLD FILE
        $file_delete = MainFile::where('task_id',$request->task_id)->delete();

		//UPDATE FILE
		if($request->list_file != ""){
			foreach ($request->list_file as $key => $file) {

                $file_name = ImagesHelper::uploadImage2($file,$current_month);
                $file_arr[] = [
                    'name' => $file_name,
                    'name_origin' => $file->getClientOriginalName(),
                    'tracking_id' => $tracking_create->id,
                    'task_id' => $request->task_id
                ];
            }
            //INSERT NEW FILE
            $file_create = MainFile::insert($file_arr);
            if(!isset($file_create) || !isset($task_update) || !isset($tracking_create) || !isset($file_delete)){
            	DB::callback();
            	return response(['status'=>'error','message'=>'Failed!']);
            }else{
            	DB::commit();
            	return response(['status'=>'success','message'=>'Successfully']);
            }
		}

		if( !isset($task_update) || !isset($tracking_create)){
        	DB::callback();
        	return response(['status'=>'error','message'=>'Failed!']);
        }else{
        	DB::commit();
        	return response(['status'=>'success','message'=>'Successfully']);
        }
	}
}