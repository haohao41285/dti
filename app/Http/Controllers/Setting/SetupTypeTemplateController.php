<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use App\Models\PosTemplateType;
use Gate;

class SetupTypeTemplateController  extends Controller
{
	public function index(){
	    if(Gate::denies('permission','setup-type-template'))
	        return doNotPermission();
		return view('setting.setup-template-type');
	}

	public function getDatatable(Request $request){
		return PosTemplateType::getDataTableByType($request->type);
	}
	/**
	 * save coupon type Template
	 * @param  Request $request
	 * @return json
	 */
	public function save(Request $request){
		$arr = [
			'template_type_name' => $request->name,
			'template_type_table_type' => $request->type,
		];

		if($request->action == "Create"){
			PosTemplateType::create($arr);
		}

		if($request->action == "Update"){
			$counpon = PosTemplateType::getById($request->typeId);
			$counpon->update($arr);
		}

		return response()->json(['status'=>1,'msg'=>$request->action." successfully"]);
	}

	public function delete(Request $request){
		if($request->id){
			$counpon = PosTemplateType::deleteById($request->id);

			return response()->json(['status'=>1,'msg'=>"deleted successfully"]);
		}
	}
}
