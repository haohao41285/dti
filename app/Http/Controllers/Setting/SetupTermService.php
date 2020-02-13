<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainTermService;
use App\Models\MainComboService;
use DataTables;
use App\Helpers\ImagesHelper;
use Auth;

class SetupTermService extends Controller
{
    public function index(){
    	$data['services'] = MainComboService::where([['cs_status',1],['cs_type',2]])->get();
    	return view('setting.setup-term-service',$data);
    }
    public function datatable(Request $request){
    	$term_services = MainTermService::leftjoin('main_user',function($join){
    		$join->on('main_term_service.created_by','main_user.user_id');
    	})
    	->join('main_combo_service',function($join){
    		$join->on('main_term_service.service_id','main_combo_service.id');
    	})
    	->select('main_user.user_nickname','main_term_service.*','main_combo_service.cs_name')
    	->get();
    	return DataTables::of($term_services)
    		->editColumn('created_at',function($row){
    			return format_datetime($row->created_at) . " by " .$row->user_nickname;
    		})
    		->editColumn('status',function($row){
    			$check = '';
    			if($row->status == 1) $check = 'checked';

    			return '<div class="custom-control custom-switch">
						    <input type="checkbox" '.$check.' class="custom-control-input" id="switch'.$row->id.'">
						    <label class="custom-control-label" for="switch'.$row->id.'"></label>
						 </div>';
    		})
    		->addColumn('action',function ($row){
                return '<a class="btn btn-sm btn-delete"  href="javascript:void(0)" title="Delete"><i class="fas fa-trash"></i></a>';
            })
            ->addColumn('service_name',function($row){
            	return $row->cs_name;
            })
            ->rawColumns(['created_at','status','action'])
    		->make(true);
    }
    function getFiles(){
    	$files = MainTermService::whereNotNUll('file_name')->distinct('file_name')->get();
    	return response(['files'=>$files]);
    }
    function uploadFile(Request $request){

    	if($request->image){
    		$file_name = ImagesHelper::uploadImage2($request->image,'term_service','file/');
    		if(!$file_name)
    			return response(['status'=>'error','message'=>'Failed! Upload File Error!']);
    		return response(['status'=>'success','file_name'=>$file_name]);
    	}
    }
    public function save(Request $request){

		$check_existed = MainTermService::where([['id','!=',$request->id],['service_id',$request->service_id]])->count();
		if($check_existed > 0)
			return response(['status'=>'error','message'=>'Failed! Existed Service!']);

    	$term_service_arr = [
    		'service_id' => $request->service_id,
    		'file_name' => $request->file_name,
    	];
    	if($request->id == 0){
    		$term_service_arr['created_by'] = Auth::user()->user_id;
    		$update_term_sevice = MainTermService::create($term_service_arr);
    	}else{
    	}
    	if(!$update_term_sevice)
    		return response(['status'=>'error','message'=>'Failed! Save Term Service Error!']);
    	return response(['status'=>'success','message'=>'Successfully! Save Term Successfully!']);
    }
    function changeStatus(Request $request){

    	$term_service_info = MainTermService::find($request->id);
    	if($term_service_info->status == 1)
    		$status_update = 0;
    	else
    		$status_update = 1;
    	$update_term_sevice = $term_service_info->update(['status'=>$status_update]);

    	if(!$update_term_sevice)
    		return response(['status' => 'error','message' => 'Failed! Change Status Failed!']);
    	return response(['status' => 'success', 'message' => 'Successfully!']);
    }
    function destroy(Request $request){
    	$update_term_service = MainTermService::find($request->id);
    	$update_term_service = $update_term_service->delete();
    	if(!$update_term_service)
    		return response(['status'=>'error','message'=>'Failed! Delete Failed!']);
    	return response(['status'=>'success','message'=>'Successfully! Delete Successfully!']);
    }
}
