<?php

namespace App\Http\Controllers\Statistics;

use Illuminate\Http\Request;
use App\Models\MainCustomer;
use App\Http\Controllers\Controller;
use App\Models\MainComboServiceBought;

class ServiceController extends Controller
{
	function __construct(){
		$date = get_nowDate();

		// MainComboServiceBought::getDatatable($date);
	}

	public function index(){
		return view('statistics.service');
	}

	public function datatable(Request $request){
		
	}
}