<?php

namespace App\Http\Controllers\Webbuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PosService;
use yajra\Datatables\Datatables;

class ServiceController extends Controller
{
    public function index(Request $request){

        $service_cate = $request->search_service_cate;
        $service_status = $request->search_service_status;
        $service_booking = $request->search_service_booking;
        $servicelist = PosService::join("pos_cateservice",function($join){

                                        $join->on("pos_service.service_cate_id","=","pos_cateservice.cateservice_id")
                                            ->on("pos_service.service_place_id","=","pos_cateservice.cateservice_place_id");
                                        })
                                    ->leftjoin("pos_user",function($join1){

                                        $join1->on("pos_service.updated_by","=","pos_user.user_id")
                                            ->on("pos_service.service_place_id","=","pos_user.user_place_id");
                                        })
                                        ->where('pos_service.service_place_id',$request->place_id)
                                        ->where('pos_service.service_status',1);

        if($service_cate>0){

           $servicelist->where('pos_service.service_cate_id',$service_cate);
        }
        if($service_status!=""){

            $servicelist->where('pos_service.enable_status',$service_status);  
        }
        if($service_booking!=""){

            $servicelist->where('pos_service.booking_online_status',$service_booking);
        }

        $servicelist->select('pos_service.*' ,'pos_cateservice.cateservice_name','pos_user.user_nickname')
            ->get();

        return Datatables::of($servicelist)

            ->editColumn('service_id', function ($row) 
            {
                return '<div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input delete" value="'.$row->service_id.'" id="a'.$row->service_id.'" name="delete[]">
                    <label class="custom-control-label" for="a'.$row->service_id.'">'.$row->service_id.'</label>
                  </div>';
            })
            ->editColumn('service_name', function ($row) 
            {
                return '<a href="'.route('edit-service',$row->service_id).'" >'.$row->service_name.'</a>';
            })
            // ->addColumn('delete', function($row)
            // {
            //     return "<input type='checkbox' name='delete[]' class='delete' id='a".$row->service_id."' value='".$row->service_id."'/>";
            // })
            ->addColumn('action1', function($row){
                $checked="";
                if($row->enable_status == 1){
                    $checked= "checked";
                }
                return "<input type='checkbox' name='service_enable_status' value='".$row->service_id."' class='status js-switch'" .$checked. "/> <input type='hidden' name='service_id' value='".$row->service_id."'>";
            })
            ->addColumn('action2', function($row){
                $checked1="";
                if($row->booking_online_status==1){
                    $checked1= "checked";
                }
                return "<input type='checkbox' name='booking_online_status' value='".$row->service_id."' class='online_booking js-switch switchery switchery-small'" .$checked1. "/><input type='hidden' name='service_id' value='".$row->service_id."'>";
            })
            ->editColumn('updated_at', function ($row) 
            {
                return format_datetime($row->updated_at)." by ".$row->user_nickname;
            })
            ->addColumn('action', function($row){
                return " <a href='".route('edit-service',$row->service_id)."' class='edit-service btn btn-sm btn-secondary delete' ><i class='fa fa-pencil fa-lg'></i> </a> <a href='javascript:void(0)' class='btn btn-sm btn-secondary delete-service' id='".$row->service_id."' data-type='user'><i class='fa fa-trash-o fa-lg'></i></a>" ;
            })
            ->rawColumns(['service_id' , 'service_name', 'action1','action','action2','delete'])
            ->make(true);
    }
}
