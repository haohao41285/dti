<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainNews;
use App\Models\MainNewsType;
use DataTables;
use Validator;
use Auth;


class Newscontroller extends Controller
{
	public function index(){
		return view('marketing.news');
	}

	public function getNewsTypeDatatable(){
		return MainNewsType::getDatatable(); 
	}

	public function getNewsDatatable(Request $request){
		return MainNews::getDatatableByNewsTypeId($request->newsTypeId);
	}
}