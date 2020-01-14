<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DataTables;
use Validator;
use App\Models\MainAppBackground;
use App\Helpers\ImagesHelper;
use Gate;

class AppBackgroundController extends Controller
{
	private $appBackground;

	public function __construct(){
		$this->appBackground = new MainAppBackground;
	}

	public function index(){
		return view('tools.app-background');
	}

	public function datatable(Request $request){
		return $this->appBackground->datatable();
	}

	public function save(Request $request){

	}

	public function delete(Request $request){

	}
}