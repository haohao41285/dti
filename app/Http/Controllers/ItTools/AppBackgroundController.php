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
		$arr = [
			'image' => $image;
		];

		if($request->id){
			$this->appBackground->updateById($request->id,$arr);
		} else {
			$this->appBackground->createByArr($arr);
		}

		return response()->json(['status'=>1,'data'=>['msg'=>'saved successfully']]);
	}

	public function delete(Request $request){


		return response()->json(['status'=>1,'data'=>['msg'=>'saved successfully']]);
	}
}