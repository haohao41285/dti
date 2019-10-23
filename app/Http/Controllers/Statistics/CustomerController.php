<?php

namespace App\Http\Controllers\Statistics;

use Illuminate\Http\Request;
use App\Models\MainCustomer;
use App\Http\Controllers\Controller;
use App\Models\MainComboServiceBought;

class CustomerController extends Controller
{
	function __construct(){

	}

	public function index(){
		return view('statistics.customer');
	}

	public function datatable(Request $request){
		if(!$request->date){
			$year = format_year(get_nowDate());
		} else {
			$year = format_year($request->date);
		}		

		return MainCustomer::getDatatableNewCustomerByYear($year);
	}
}