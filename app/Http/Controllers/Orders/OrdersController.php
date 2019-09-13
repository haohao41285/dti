<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Option;
use App\Helpers\GeneralHelper;
use App\Models\MainCustomerTemplate;
use App\Models\MainComboService;
use App\Models\MainComboServiceBought;
use App\Models\MainCustomerService;
use App\Models\MainTeam;
use App\Models\PosPlace;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Carbon\Carbon;
use Validator;
use Auth;
use DB;

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
		return view('orders.sellers',$data);
	}

	public function add($customer_id =1){
		if(!$customer_id)
            return back()->with('error','Error!');

        $customer_info = MainCustomerTemplate::where('id',$customer_id)->first();

        $combo_service_list = MainComboService::where('cs_status',1)->orderBy('cs_type','asc')->get();

        $count = round($combo_service_list->count()/2);

        return view('orders.add',compact('customer_info','combo_service_list','count'));
	}
	function authorizeCreditCard(Request $request)
	{
		// return $request->all();
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
		$validator = Validator::make($request->all(),$rule,$message);
		if($validator->fails())
			// return back()->with('error' => $validator->getMessageBag()->toArray());
			return redirect()->back()->withErrors($validator)->withInput();
		
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
		    $customerAddress->setCompany("Souveniropolis");
		    $customerAddress->setAddress($request->address);    //"14 Main Street"
		    $customerAddress->setCity($request->city);    //"Pecan Springs"
		    $customerAddress->setState($request->state);    //"TX"
		    $customerAddress->setZip($request->zip_code);    //"44628"
		    $customerAddress->setCountry($request->country);   //"USA"
		    // Set the customer's identifying information
		    $customerData = new AnetAPI\CustomerDataType();
		    $customerData->setType("individual");
		    $customerData->setId("99999456654");
		    $customerData->setEmail("EllenJohnson@example.com");
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

		            	return back()->with(['success'=>'Transaction Successfully!']);


		                // echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
		                // echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
		                // echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
		                // echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
		                // echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
		            } else {
		            	return back()->with(['error'=>'Transaction Failed. Check again!']);
		                echo "Transaction Failed \n";
		                if ($tresponse->getErrors() != null) {
		                    // echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
		                    // echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
		                }
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
		        echo  "No response returned \n";
		    }
	        return $response;
		}else{
		$combo_service_arr = [];
		$number_credit =  substr($request->credit_card_number, 0,4).substr($request->credit_card_number, -4);
		//CHECK SERVICE OR COMBO
		foreach ($request->cs_id as $key => $value) {
			$service_list = MainComboService::where('id',$value)->first()->cs_service_id;
			if($service_list != NULL)
				$combo_service_arr = array_merge(explode(";",$service_list),$combo_service_arr);
			else
				$combo_service_arr[] = $value;
		}
		return $combo_service_arr;

		$combo_service_list = implode(";",$request->cs_id);

			DB::beginTransaction();
			if($request->place_id != 0){
				$place_id = $request->place_id;
			}else{
				$place_id = PosPlace::where('place_customer_id',$request->customer_id)->max('place_id')+1;
			}
			$cs_id = MainCustomerService::where('cs_place_id',$place_id)->max('cs_id')+1;

			//ORDER ARRAY
			$order_arr = [];
			foreach ($request->cs_id as $key => $value) {
				//CHECK CUSTOMER SERVICE EXIST 
				$check = MainCustomerService::where('cs_place_id',$place_id)
											->where('cs_customer_id',$request->customer_id)
											->where('cs_service_id',$value)
											->first();
				if(isset($check))
				{
					$cs_date_expire = $check->cs_date_expire;
					// return Carbon::parse($cs_date_expire)->add($check->getComboService()->);
					// MainCustomerService::where('cs_place_id',$place_id)
					// 					->where('cs_customer_id',$request->customer_id)
					// 					->where('cs_service_id',$value)
					// 					->update([''])
				}
				$order_arr[] = [
					'cs_id' => $cs_id,
					'cs_place_id' => $place_id,
					'cs_customer_id' => $request->customer_id,
					'cs_service_id' => $value,
					''
				];
			}
				
			// MainCustomerService

			//ORDER HISTORY ARRAY
			$order_history_arr = [
				'csb_customer_id' => $request->customer_id,
				'csb_combo_service_id' => $combo_service_list,
				'csb_amount' => $request->service_price_hidden,
				'csb_charge' => $request->payment_amount_hidden,
				'csb_cashback' => 0,
				'csb_payment_method' => 3,
				'csb_card_type' => $request->credit_card_type,
				'csb_amount_deal' => $request->discount,
				'csb_card_number' => $number_credit,
				'csb_status' => 1
			];
			//INSERT NEW ORDER
			$insert_order = MainComboServiceBought::insert($order_history_arr);
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
			
			if(!isset($insert_order) || !isset($update_team_customr_status)){
				DB::callback();
				return back()->with(['error','Transaction Failed. Check again!']);
			}
			else{
				DB::commit();
				return back()->with(['success','Transaction Successfully!']);
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
	    $place_list = PosPlace::join('main_customer_template',function($join){
	    	$join->on('pos_place.place_customer_id','main_customer_template.id');
	    })
	    	->where(function($query) use ($customer_phone){
				$query->where('main_customer_template.ct_business_phone',$customer_phone)
				->orWhere('main_customer_template.ct_cell_phone',$customer_phone);
			})
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
}