<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DataTables;
use DB;
use App\Models\WebService;
use App\Models\WebServiceType;
use App\Helpers\ImagesHelper;
use Gate;


class DesignController extends Controller
{
    public function index(){
        if(Gate::allows('permission','web-service'))
            return view('tools.design-image');
        else
            return doNotPermission();
    }
    public function datatable(Request $request){
        $web_services = DB::table('web_service_types');
        return DataTables::of($web_services)
        ->addColumn('action',function($row){
            return '<a class="btn btn-sm btn-secondary" web-service="'.$row->web_service_type_id.'" href="'.route('web_service.edit',$row->web_service_type_id).'" data-toggle="tooltip" title="Edit Service Type"><i   class="fas fa-edit"></i> </a>
            <a class="btn btn-sm btn-secondary delete-service-type" web-service="'.$row->web_service_type_id.'" href="javascript:void(0)" data-toggle="tooltip" title="Delete Service Type"><i   class="fas fa-trash"></i> </a>';
        })
        ->editColumn('web_service_type_status',function($row){

            $row->web_service_type_status == 1?$check = 'checked':$check="";

            return ' <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input changeStatus" '.$check.' id="service_'.$row->web_service_type_id.'"
                        value="'.$row->web_service_type_status.'" service-type="'.$row->web_service_type_id.'">
                        <label class="custom-control-label" for="service_'.$row->web_service_type_id.'"></label>
                    </div>';
        })
        ->rawColumns(['action','web_service_type_status'])
        ->make(true);
    }
    public function save(Request $request){
        
        $web_service_type_name = $request->web_service_type_name;
        $web_service_image = $request->web_service_image;
        $id = $request->web_service_type_id;

        DB::beginTransaction();
        $update = 1;
        $service = 1;

        if($id == 0){
            //Add new service type
            //Check existed
            $check = WebServiceType::where('web_service_type_name',$web_service_type_name)->count();
            if($check == 0){
                $service_type_arr = [
                    'web_service_type_name' => $web_service_type_name,
                    'web_service_type_status' => 1,
                ];
                try{
                    $update_service_type = WebServiceType::create($service_type_arr);
                    $service_type = $update_service_type->web_service_type_id;
                }
                catch(\Exception $e){
                    $update = 0;
                }
            }else{
                DB::rollBack();
                return back()->with(['error'=>'Failed! Service Type Name Existed']);
            }
        }
        elseif($id != 0){
            //Edit old service type
            //check other existed
            $service_type = $id;
            $check = WebServiceType::where('web_service_type_id','!=',$id)->where('web_service_type_name',$web_service_type_name)->count();
            if($check == 0){
                try{
                    $update_service_type = WebServiceType::where('web_service_type_id',$id)->update(['web_service_type_name'=>$web_service_type_name]);
                }
                catch(\Exception $e){
                    $update = 0;
                }
            }else{
                DB::rollBack();
                return back()->with(['error'=>'Failed! Service Type Name Existed']);
            }
        }
        //Update serivce image
        if(isset($web_service_image) && count($web_service_image) > 0 ){
            $service_arr = [];
            foreach($web_service_image as $service_image){
                $service_arr[] = [
                    'web_service_type_id' => $service_type,
                    'web_service_image' => $service_image,
                    'web_service_status' => 1
                ];
            }
            try{
                WebService::insert($service_arr);
            }
            catch(\Exception $e){
                $service = 0;
            }
        }
        if($update == 1 &&  $service == 1){
            DB::commit();
            return redirect()->route('web_service.index')->with(['success'=>'Successfully!']);
        }else{
            DB::rollBack();
            return back()->with(['error'=>'Failed!']);
        }
    }
    public function edit($id = 0){
        if(!$id)
            return back()->with(['error'=>'Error!']);
        
        $data['id'] = $id;
        if($id != 0){
            $data['service_type'] = WebServiceType::where('web_service_type_id',$id)->first();
        }
        return view('tools.web-service-type-edit',$data);
    }
    public function uploadMultiImages(Request $request)
    {
        if ($request->hasFile('file')) {

            $imageFiles = $request->file('file');

            $image_name = [];

            foreach ($request->file('file') as $fileKey => $fileObject ) {

                if ($fileObject->isValid()) {

                    $image_name[] = ImagesHelper::uploadImageDropZone_get_path($fileObject,'service','web_service');
                }
            }
            return $image_name;
        }
            return "upload error";
    }
    public function deleteService(Request $request){
        //Check service in web order
        $check = DB::table('web_order')->where([['web_order_type',2],['web_service_id',$request->service_id],['web_order_status',1]])->count();
        if($check > 0 )
            return response(['status'=>'error','message'=>'Failed! This Service use in order. Check again!']);
        try{
            WebService::where('web_service_id',$request->service_id)->delete();
            return response(['status'=>'success','message'=>'Successfully!']);
        }
        catch(\Exception $e){
            \Log::info($e);
            return response(['status'=>'error','message'=>'Failed!']);
        }

    }
    public function delete(Request $request){
        $service_type = $request->service_type;
        //Check service inside service type
        $check = WebService::where('web_service_type_id',$service_type)->count();
        if($check > 0 )
            return response(['status'=>'error','message'=>'Failed! Can Not delete service type. It includes services']);
        try{
            WebServiceType::where('web_service_type_id',$service_type)->delete();
            return response(['status'=>'success','message'=>'Successfully!']);
        }
        catch(\Exception $e){
            \Log::info($e);
            return response(['status'=>'error','message'=>'Failed!']);
        }
    }
    public function changeStatus(Request $request){
        ($request->service_status == 1)?$update_status=0:$update_status=1;
        try{
            $update_service_type = WebServiceType::where('web_service_type_id',$request->service_type)->update(['web_service_type_status'=>$update_status]);
            return response(['status'=>'success','message'=>'Successfully!']);
        }
        catch(\Exception $e){
            \Log::info($e);
            return response(['status'=>'error','message'=>'Failed!']);
        }
    }
}
