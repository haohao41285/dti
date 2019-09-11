<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Helpers\Option;
use App\Helpers\GeneralHelper;


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

	public function add(){
		return view('orders.add');
	}
}