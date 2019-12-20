<?php

namespace App\Http\Controllers\Setting;

use App\Models\MainComboServiceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainComboService;
use App\Models\PosMerchantMenus;
use App\Models\MainUser;
use Validator;
use DataTables;
use DB;
use Auth;
use Gate;
use App\Models\MainMenuAppINailSo;

class SetupServiceController extends Controller
{
	public function setupService(Request $request){

	    if(Gate::denies('permission','setup-service-read'))
	        return doNotPermission();

		return view('setting.setup-service');
	}
    public function serviceDatabase(Request $request)
	{
        if(Gate::denies('permission','setup-service-read'))
            return doNotPermission();

		$combo_service_arr = [];
//		$service_combo_list = MainComboService::leftjoin('main_user',function($join){
//			$join->on('main_combo_service.cs_assign_to','main_user.user_id');
//		})
//			->select('main_combo_service.*','main_user.user_nickname','main_user.user_id')
//			->get();
        $service_combo_list = MainComboService::where('cs_status',1)->get();

		foreach ($service_combo_list as $key => $service_combo) {

			$service_name_arr = "";

			if($service_combo->cs_service_id != NULL){

				$service_id = explode(";",$service_combo->cs_service_id);

				$service_name = MainComboService::whereIn('id',$service_id)->get();

				foreach ($service_name as $key => $value) {
					$service_name_arr .= "<span>- ".$value->cs_name."</span><br>";
				}
			}
            $cs_assign_to = "";
            $cs_assign_id = [];
			if(!empty($service_combo->cs_assign_to)){
			    $assign_arr = explode(';',$service_combo->cs_assign_to);
			    $assign_list = MainUser::whereIn('user_id',$assign_arr)->select('user_id','user_nickname','user_lastname','user_firstname')->get();
			    foreach($assign_list as $assign){
                    $cs_assign_to .= "<span class='text-capitalize'>".$assign->user_nickname."(".$assign->getFullname().")</span><br>";
                    $cs_assign_id[] = $assign->user_id;
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
				'cs_assign_to' => $cs_assign_to,
				'cs_assign_id' => implode(';',$cs_assign_id),
				'cs_status' => $service_combo->cs_status,
                'cs_form_type' => $service_combo->cs_form_type,
                'cs_combo_service_type' => $service_combo->getComboServiceType['name'],
                'cs_expiry_period' => $service_combo->cs_expiry_period,
                'cs_combo_service_type_id' => $service_combo->cs_combo_service_type
			];
		}

		return DataTables::of($combo_service_arr)

//		    ->editColumn('cs_combo_service_type',function($row){
////		    	return getFormService()[$row['cs_combo_service_type']];
//		    	foreach(getFormService() as $key => $form_service){
//		    	    if($key == $row['cs_combo_service_type'])
//		    	        return $form_service;
//                }
//		    })
		    ->addColumn('cs_status',function($row){
				if($row['cs_status'] == 1) $checked='checked';
	       		else $checked="";
				return '<input type="checkbox" cs_id="'.$row['id'].'" cs_status="'.$row['cs_status'].'" class="js-switch"'.$checked.'/>';
			})
			->addColumn('action',function($row){
				return '<a class="btn btn-sm btn-secondary edit-cs"
				 cs_expiry_period="'.$row['cs_expiry_period'].'"
				 cs_combo_service_type="'.$row['cs_combo_service_type_id'].'"
				 cs_form_type="'.$row['cs_form_type'].'" cs_price='.$row['cs_price'].'
				 cs_description="'.$row['cs_description'].'" 
				 cs_type='.$row['cs_type'].'
				 cs_name="'.$row['cs_name'].'" 
				 cs_id="'.$row['id'].'"  
				 cs_assign_id="'.$row['cs_assign_id'].'"
				 title="Edit" href="javascript:void(0)"><i class="fas fa-edit"></i></a>';
			})
			->rawColumns(['cs_status','action','cs_service_id','cs_assign_to'])
		    ->make(true);
	}
	public function changeStatusCs(Request $request){

        if(Gate::denies('permission','setup-service-update'))
            return doNotPermissionAjax();

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
	    //GET ALL USER
		$data['user'] = MainUser::where('user_status',1)->get();
		$data['service_form'] = getFormService();
		$data['combo_service_type_list'] = MainComboServiceType::all();

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

			$cs_info = MainComboService::find($cs_id);
			//GET MENU ID LIST
			$cs_menu_id = $cs_info->cs_menu_id;
			$menu_id_arr = explode(";", $cs_menu_id);

			if($cs_info->cs_app_website_type == 1){

				$menu_list = PosMerchantMenus::orderBy('mer_menu_index','asc')->get();
				$menu_list = collect($menu_list);
				$menu_parents = $menu_list->where('mer_menu_parent_id',0);

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
			}
			elseif($cs_info->cs_app_website_type == 2){

				$menus = MainMenuAppINailSo::active()->get();

				foreach ($menus as $key => $menu) {
					$check = "";
					$id = ''.$menu->id;
					if(in_array($menu->id, $menu_id_arr))
						$check = "checked";
					$menu_html .= '<div class="checkbox">
		                    <label><input type="checkbox" '.$check.'  class="service_id" id="'.$id.'"  style="height: 20px;width: 20px" value="'.$menu->id.'">'.$menu->name.'</label>
		                </div>';
				}
			}
			if($menu_html != ""){
				$data['menu_html'] = $menu_html;
				return response($data);
			}
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
                    <label style="margin-left:30px"><input type="checkbox"  name="cs_menu_id[]"  '.$check.' parent_id="'.$menu_parent_id.'"  class="service_id '.$menu_parent_id.'"  style="height: 20px;width: 20px" value="'.$menu_son->mer_menu_id.'">'.$menu_son->mer_menu_text.'</label>
                </div>';

            $menu_html .= self::getMenuSon($menu_list,$menu_son->mer_menu_id,$menu_id_arr);
		}
		return $menu_html;
	}

	public function saveServiceCombo(Request $request)
	{
//	    return $request->all();
        if(Gate::denies('permission','setup-service-update'))
            return doNotPermissionAjax();

        $cs_id = $request->cs_id;
		$cs_type = $request->cs_type;
		$cs_name = $request->cs_name;
		$cs_price = $request->cs_price;
		$cs_description = $request->cs_description;
		$cs_assign_to = $request->cs_assign_to!=""?implode(';',$request->cs_assign_to):"";
		$cs_form_type = $request->cs_form_type;
		$cs_combo_service_type = $request->cs_combo_service_type;

		$rule = [
            'cs_name' => 'required',
            'cs_price' => 'required|numeric',
        ];
		if($request->cs_type == 1){
		    $rule['cs_service_id'] = 'required';
        }
		if($request->cs_type == 2){
		    $rule['cs_expiry_period'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(),$rule);

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

        if(isset($request->cs_service_id))
            $service_id_list = implode(';',$request->cs_service_id);
        else
            $service_id_list = "";

        if(isset($request->cs_menu_id))
            $cs_menu_id = implode(';',$request->cs_menu_id);
        else
            $cs_menu_id = '';

        if($cs_id == 0)
            $cs_update = MainComboService::insert([
                'cs_name'=>$cs_name,
                'cs_service_id'=>$service_id_list,
                'cs_menu_id'=>$cs_menu_id,
                'cs_expiry_period' => $request->cs_expiry_period,
                'cs_price'=>$cs_price,
                'cs_type'=>$cs_type,
                'cs_status'=>1,
                'cs_description'=>$cs_description,
                'cs_assign_to'=>$cs_assign_to,
                'cs_combo_service_type'=>$cs_combo_service_type,
                'cs_form_type' => $cs_form_type,
                'cs_app_website_type' => $request->cs_app_website_type??null
            ]);
        else{
            if($cs_type == 1)
                $cs_update = MainComboService::where('id',$cs_id)->update([
                    'cs_name'=>$cs_name,
                    'cs_price' => $cs_price,
                    'cs_service_id'=>$service_id_list,
                    'cs_description'=>$cs_description,
                    'cs_assign_to'=>$cs_assign_to,
                    'cs_combo_service_type' => $cs_combo_service_type,
                    'cs_form_type' => $cs_form_type
                ]);
            else
                $cs_update = MainComboService::where('id',$cs_id)->update([
                    'cs_name'=>$cs_name,
                    'cs_price' => $cs_price,
                    'cs_menu_id'=>$cs_menu_id,
                    'cs_description'=>$cs_description,
                    'cs_assign_to'=>$cs_assign_to,
                    'cs_combo_service_type' => $cs_combo_service_type,
                    'cs_form_type' => $cs_form_type,
                    'cs_expiry_period' => $request->cs_expiry_period
                ]);
        }
		if(!isset($cs_update))
			return response(['status'=>'error','message'=>'Error!Check Again.']);
		else
			return response(['status'=>'success','message'=>'Success!']);
	}
	public function getCs(Request $request)
	{
		$cs_type = $request->cs_type;

		$data['user'] = MainUser::where('user_status',1)->get();
		$data['cs_combo_service_type'] = MainComboServiceType::active()->get();

		if($cs_type == 1){
			$data['cs_list'] = MainComboService::where('cs_type',2)->where('cs_status',1)->get();

			if(!isset($data['cs_list']))
			return response(['status'=>'error','message'=>'Error']);
		else
			return response($data);
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
	                    <label><input type="checkbox" name="cs_menu_id[]" '.$check.' parent_id="0" class="service_id " id="'.$id.'"  style="height: 20px;width: 20px" value="'.$menu_parent->mer_menu_id.'">'.$menu_parent->mer_menu_text.'</label>
	                </div>';
	             $menu_html .= self::getMenuSon($menu_list,$menu_parent->mer_menu_id,$menu_id_arr);
			}
			$data['menu_html'] = $menu_html;

			return response($data);
		}
	}
    public function setServiceType (){

        if(Gate::denies('permission','setup-service-type-read'))
            return doNotPermission();

        return view('setting.setup-service-type');
    }
    public function serviceTypeDatatable(Request $request){
	    $service_type_list = MainComboServiceType::all();
	    return DataTables::of($service_type_list)
            ->addColumn('status',function($row){
                if($row->status == 1) $checked='checked';
                else $checked="";
                return '<input type="checkbox" id="'.$row->id.'" status="'.$row->status.'" class="js-switch"'.$checked.'/>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }
    public function changeStatusServiceType(Request $request){

        if(Gate::denies('permission','setup-service-type-update'))
            return doNotPermissionAjax();

        $id = $request->id;
	    $status = $request->status;
	    if($status == 1)
	        $status_update = 0;
	    else
	        $status_update = 1;
	    $update_service_type = MainComboServiceType::find($id)->update(['status'=>$status_update]);

	    if(!isset($update_service_type))
	        return response(['status'=>'error','message'=>'Change Status Failed!']);
	    else
	        return response(['status'=>'success','message'=>'Change Status Successfully!']);
    }
    public function addServiceType(Request $request){

	    $id = $request->service_type_id;
	    if($request->name == "")
	        return response(['status'=>'error','message'=>'Enter Name']);

        $input = $request->all();
	    if($id == 0){
	        $input['created_by'] = Auth::user()->user_id;
	        $input['status'] = 1;
	        $service_type_update = MainComboServiceType::create($input);
        }else{
	        $input['updated_by'] = Auth::user()->user_id;
	        $service_type_update  =MainComboServiceType::find($id)->update($input);
        }
	    if(!isset($service_type_update))
	        return response(['status'=>'error','message'=>'Save Service Type Failed!']);
	    else
	        return response(['status'=>'success','message'=>'Save Service Type Successfully!']);
    }
    public function menuApp(Request $request){

    	$type_app = $request->type_app;
    	$menu_html = "";

    	if($type_app == 1){
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
	                    <label><input type="checkbox" name="cs_menu_id[]" '.$check.' parent_id="0" class="service_id " id="'.$id.'"  style="height: 20px;width: 20px" value="'.$menu_parent->mer_menu_id.'">'.$menu_parent->mer_menu_text.'</label>
	                </div>';
	             $menu_html .= self::getMenuSon($menu_list,$menu_parent->mer_menu_id,$menu_id_arr);
			}
    	}
    	elseif($type_app == 2){

    		$menus = MainMenuAppINailSo::active()->get();

    		foreach ($menus as $key => $menu) {
    			$menu_html .= '<div class="checkbox">
	                    <label><input type="checkbox" name="cs_menu_id[]" class="service_id " id="'.$menu->id.'"  style="height: 20px;width: 20px" value="'.$menu->id.'">'.$menu->name.'</label>
	                </div>';
    		}
    	}
		$data['menu_html'] = $menu_html;

		return $data;
    }
}
