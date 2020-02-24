<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainUser;
use App\Models\PosPlace;
use App\Models\MainComboService;

class OldOrderController extends Controller
{
    public function index(){
    	
    	$data['user_list'] = MainUser::active()->orderBy('user_lastname','asc')->get();
    	$data['place_list'] = PosPlace::active()->orderBy('place_name','asc')->get();
    	$data['service_list'] = MainComboService::active()->orderBy('cs_name','asc')->get();

    	return view('orders.old_order',$data);
    }
}
