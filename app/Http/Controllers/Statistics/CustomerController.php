<?php

namespace App\Http\Controllers\Statistics;

use Illuminate\Http\Request;
use App\Models\MainCustomer;
use App\Http\Controllers\Controller;
use App\Models\MainComboServiceBought;
use App\Traits\StatisticsTrait;

class CustomerController extends Controller
{
	function __construct(){

	}

	public function index(){
		return view('statistics.customer');
	}

	public function datatable(Request $request){
		$type = $request->type;
		$valueQuarter = $request->valueQuarter;
		$date = format_date_db($request->date) ?? null;	

		return MainCustomer::getDatatableStatistic($type, $valueQuarter, $date);
	}
}