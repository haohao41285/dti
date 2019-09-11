<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainComboService;
use App\Models\PosMerchantMenus;
use Validator;
use DataTables;
use DB;

class SetupServiceController extends Controller
{
	public function setupService(Request $request){

		return view('setting.setup-service');
	}
    public function serviceDatabase(Request $request)
	{
		$combo_service_arr = [];
		$service_combo_list = MainComboService::all();

		foreach ($service_combo_list as $key => $service_combo) {

			$service_name_arr = "";

			if($service_combo->cs_service_id != NULL){

				$service_id = explode(";",$service_combo->cs_service_id);

				$service_name = MainComboService::whereIn('id',$service_id)->get();

				foreach ($service_name as $key => $value) {
					$service_name_arr .= "<span>- ".$value->cs_name."</span><br>";
				}
			}
			$combo_service_arr[] = [
				'id' => $service_combo->id,
				'cs_name' => $service_combo->cs_name,
				'cs_price' => $service_combo->cs_price,
				'cs_expiry_period' => $service_combo->cs_expiry_period,
				'cs_service_id' => $service_name_arr,
				'cs_description' => $service_combo->cs_description,
				'cs_type' => $service_combo->cs_type,
				'cs_status' => $service_combo->cs_status,
			];
		}

		return DataTables::of($combo_service_arr)

		    ->editColumn('cs_type',function($row){
		    	if($row['cs_type'] == 1)
		    		return "Combo";
		    	else
		    		return "Service";
		    })
		    ->addColumn('cs_status',function($row){
				if($row['cs_status'] == 1) $checked='checked';
	       		else $checked="";
				return '<input type="checkbox" cs_id="'.$row['id'].'" cs_status="'.$row['cs_status'].'" class="js-switch"'.$checked.'/>';
			})
			->addColumn('action',function($row){
				return '<a class="btn btn-sm btn-secondary edit-cs" cs_price='.$row['cs_price'].' cs_description="'.$row['cs_description'].'" cs_type='.$row['cs_type'].' cs_name="'.$row['cs_name'].'" cs_id="'.$row['id'].'"  title="Edit" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                <a class="btn btn-sm btn-secondary delete-team" title="Delete" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
			})
			->rawColumns(['cs_status','action','cs_service_id'])
		    ->make(true);
	}
	public function changeStatusCs(Request $request){

		$cs_id = $request->cs_id;
		$cs_status = $request->cs_status;

		if(!isset($cs_id))
			return response(['status'=>'error','message'=>'Change Error!']);

		if($cs_status == 1)
			$status = 0;
		else
			$status = 1;
		$cs_update = MainComboService::where('id',$cs_id)->update(['cs_status'=>$status]);

		if(!isset($cs_update))
			return response(['status'=>'error','message'=>'Change Error!']);
		else
			return response(['status'=>'success','message'=>'Change Success!']);
	}
	public function getServiceCombo(Request $request)
	{


		$cs_id = $request->cs_id;
		$cs_type = $request->cs_type;

		if(!isset($cs_id))
			return response(['status'=>'error','message'=>'Error!']);

		if($cs_type == 1){//COMBO

			$cs_info = MainComboService::find($cs_id);

			$service_list = $cs_info->cs_service_id;

			$data['service_arr'] = explode(";", $service_list);

			$data['service_list_all'] = MainComboService::where('cs_type',2)->where('cs_status','!=',0)->get();

			if(!isset($data))
				return response(['status'=>'error','message'=>'Error!']);

			return $data;
		}
		else{//SERVICE
			$menu_html = "";
			$menu_list = PosMerchantMenus::orderBy('mer_menu_index','asc')->get();
			$menu_list = collect($menu_list);
			$menu_parents = $menu_list->where('mer_menu_parent_id',0);

			//GET MENU ID LIST
			$cs_info = MainComboService::find($cs_id);
			$cs_menu_id = $cs_info->cs_menu_id;
			$menu_id_arr = explode(";", $cs_menu_id);
			
			
			foreach ($menu_parents as $key => $menu_parent) {
				$check = "";
				$id = ''.$menu_parent->mer_menu_id;
				if(in_array($menu_parent->mer_menu_id, $menu_id_arr))
					$check = "checked";
				$menu_html .= '<div class="checkbox">
	                    <label><input type="checkbox" '.$check.' parent_id="0" class="service_id " id="'.$id.'"  style="height: 20px;width: 20px" value="'.$menu_parent->mer_menu_id.'">'.$menu_parent->mer_menu_text.'</label>
	                </div>';
	             $menu_html .= self::getMenuSon($menu_list,$menu_parent->mer_menu_id,$menu_id_arr);

			}
			if($menu_html != "")
				return response(['menu_html'=>$menu_html]);
			else
				return response(['status'=>'error','message'=>'Error!']);
		}
	}

	public static function getMenuSon($menu_list,$menu_parent_id,$menu_id_arr,$menu_html = "")
	{
		$menu_sons = $menu_list->where('mer_menu_parent_id',$menu_parent_id);

		foreach ($menu_sons as $key => $menu_son) {

			$check = "";
				$id = ''.$menu_son->mer_menu_id;
				if(in_array($menu_son->mer_menu_id, $menu_id_arr))
					$check = "checked";

			$menu_html .= '<div class="checkbox">
                    <label style="margin-left:30px"><input type="checkbox" '.$check.' parent_id="'.$menu_parent_id.'"  class="service_id '.$menu_parent_id.'"  style="height: 20px;width: 20px" value="'.$menu_son->mer_menu_id.'">'.$menu_son->mer_menu_text.'</label>
                </div>';

            $menu_html .= self::getMenuSon($menu_list,$menu_son->mer_menu_id,$menu_id_arr);
		}
		return $menu_html;
	}

	public function saveServiceCombo(Request $request)
	{
		$cs_id = $request->cs_id;
		$cs_type = $request->cs_type;
		$cs_name = $request->cs_name;
		$cs_price = $request->cs_price;
		$cs_description = $request->cs_description;
		$service_id_arr = $request->service_id_arr;

		$rule = [
            'cs_name' => 'required',
            'service_id_arr' => 'required',
            'cs_price' => 'required',
        ];
        $message = [
        'cs_name.required' => 'Enter Combo Name, Please!',
        'service_id_arr.required' => 'Check Service, Please!',
        'cs_price.required' => 'Enter Price, Please!'
        ];

        $validator = Validator::make($request->all(),$rule,$message);

        if($validator->fails()){
            return \Response::json(array(
                'status' => 'error',
                'message' => $validator->getMessageBag()->toArray()

            ));
        }
        //CHECK NAME COMBO SERVICE
        if($cs_id != 0)
			$check = MainComboService::where('id','!=',$cs_id)->where('cs_name',$cs_name)->count();
		if($cs_id == 0)
			$check = MainComboService::where('cs_name',$cs_name)->count();

			if($check > 0)
				return response(['status'=>'error','message'=>'Error! Name has existed.']);


			$service_id_list = implode(";", $service_id_arr);

        if($cs_type == 1){
        	if($cs_id != 0)
			    $cs_update = MainComboService::where('id',$cs_id)->update(['cs_name'=>$cs_name,'cs_service_id'=>$service_id_list,'cs_description'=>$cs_description]);
			else
				$cs_update = MainComboService::insert(['cs_name'=>$cs_name,'cs_service_id'=>$service_id_list,'cs_price'=>$cs_price,'cs_type'=>1,'cs_status'=>1,'cs_description'=>$cs_description]);
        }
        else{
        	if($cs_id != 0){
        		$cs_update = MainComboService::where('id',$cs_id)->update(['cs_name'=>$cs_name,'cs_menu_id'=>$service_id_list,'cs_description'=>$cs_description]);
        	}else
        	    $cs_update = MainComboService::insert(['cs_name'=>$cs_name,'cs_service_id'=>$service_id_list,'cs_price'=>$cs_price,'cs_type'=>2,'cs_status'=>1,'cs_description'=>$cs_description]);
			
        }

		if(!isset($cs_update))
			return response(['status'=>'error','message'=>'Error!Check Again.']);
		else
			return response(['status'=>'success','message'=>'Success!']);
	}
	public function getCs(Request $request)
	{
		$cs_type = $request->cs_type;

		if($cs_type == 1){
			$cs_list = MainComboService::where('cs_type',2)->where('cs_status',1)->get();

			if(!isset($cs_list))
			return response(['status'=>'error','message'=>'Error']);
		else
			return $cs_list;
		}else{
			$menu_html = "";
			$menu_list = PosMerchantMenus::orderBy('mer_menu_index','asc')->get();
			$menu_list = collect($menu_list);
			$menu_parents = $menu_list->where('mer_menu_parent_id',0);

			$menu_id_arr = [];
			
			foreach ($menu_parents as $key => $menu_parent) {
				$check = "";
				$id = ''.$menu_parent->mer_menu_id;
				if(in_array($menu_parent->mer_menu_id, $menu_id_arr))
					$check = "checked";
				$menu_html .= '<div class="checkbox">
	                    <label><input type="checkbox" '.$check.' parent_id="0" class="service_id " id="'.$id.'"  style="height: 20px;width: 20px" value="'.$menu_parent->mer_menu_id.'">'.$menu_parent->mer_menu_text.'</label>
	                </div>';
	             $menu_html .= self::getMenuSon($menu_list,$menu_parent->mer_menu_id,$menu_id_arr);
			}
			return response(['menu_html'=>$menu_html]);
		}
		
	}
}
