<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Option;
use App\Helpers\GeneralHelper;
use App\Models\MainCustomerTemplate;
use App\Models\MainComboService;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Validator;

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

        $combo_service_list = MainComboService::where('cs_status',1)->get();

        $count = round($combo_service_list->count()/2);

        return view('orders.add',compact('customer_info','combo_service_list','count'));
	}
	function authorizeCreditCard(Request $request)
	{
		$rule = [
			'payment_amount' => 'required',
			'credit_card_type' => 'required',
			'credit_card_number' => 'required',
			'experation_month' => 'required',
			'experation_year' => 'required',
			'cvv_number' => 'required',
			'first_name' => 'required',
			'last_name' => 'required'
		];
		$message = [
			'payment_amount.required' => 'Enter Amount',
			'credit_card_type.required' => 'Choose Credit card Type',
			'credit_card_number.required' => 'Enter Card Number',
			'experation_month.required' => 'Choose Experation Date',
			'experation_year.required' => 'Choose Experation Date',
			'cvv_number.required' => 'Enter svv number',
		];
		$validator = Validator::make($request->all(),$rule,$message);
		if($validator->fails())
			// return back()->with('error' => $validator->getMessageBag()->toArray());
			return redirect()->back()->withErrors($validator)->withInput();
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


	                echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
	                echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
	                echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
	                echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
	                echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
	            } else {
	            	return back()->with(['error'=>'Transaction Failed. Check again!']);
	                echo "Transaction Failed \n";
	                if ($tresponse->getErrors() != null) {
	                    echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
	                    echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
	                }
	            }
	            // Or, print errors if the API request wasn't successful
	        } else {
	        	return back()->with(['error'=>'Transaction Failed. Check again!']);
	            echo "Transaction Failed \n";
	            $tresponse = $response->getTransactionResponse();
	        
	            if ($tresponse != null && $tresponse->getErrors() != null) {
	                echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
	                echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
	            } else {
	                echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
	                echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
	            }
	        }      
	    } else {
	        echo  "No response returned \n";
	    }
	    return $response;
	}
}