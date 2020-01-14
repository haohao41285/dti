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
		if($request->image){
			$image = ImagesHelper::uploadImageToAPI($request->image,'app/background');
		}

		$arr = [
			'image' => $image ?? null,
		];

		if($request->id){
			if(isset($image)){
				$this->appBackground->updateById($request->id,$arr);
			}
		} else {			
			$this->appBackground->createByArr($arr);			
		}

		return response()->json(['status'=>1,'data'=>['msg'=>'Saved successfully']]);
	}

	public function delete(Request $request){

		$this->appBackground->deleteById($request->id);

		return response()->json(['status'=>1,'data'=>['msg'=>'Deleted successfully']]);
	}
}