<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainDiscount;
use DataTables;
use App\Models\MainComboService;

class SetupDiscount extends Controller
{
    public function index(){
        $data['service_list'] = MainComboService::active()->orderBy('cs_name','asc')->get();
        return view('setting.setup-discount',$data);
    }
    public function datatable(Request $request){

        $discounts = MainDiscount::all();

        return DataTables::of($discounts)
        ->editColumn('amount',function($row){
            $row->type==0?$ype="%":$type="$";
            return $row->amount;
        })
        ->editColumn('date_start',function($row){
            return format_date($row->date_start);
        })
        ->editColumn('date_end',function($row){
            return format_date($row->date_end);
        })
        ->addColumn('type_amount',function($row){
            if($row->type == 1)
                return $row->amount."$";
            else
                return $row->amount."%";
        })
        ->addColumn('action',function ($row){
            return '<a class="btn btn-sm btn-delete"  href="javascript:void(0)" title="Delete"><i class="fas fa-trash"></i></a>';
        })
        ->make(true);
    }
    public function save(Request $request){
        $input = $request->all();
        if($request->id == 0){
            try{
                $input['date_start'] = format_date_db($input['date_start']);
                $input['date_end'] = format_date_db($input['date_end']);
                $input['code'] = strtoupper($input['code']);
                $input['service_list'] = $input['service_arr']!=""?str_replace(',', ';',$input['service_arr']):"";
                MainDiscount::create($input);
                return response(['status'=>'success','message'=>'Successfully!']);
            }
            catch(\Exception $e){
                \Log::info($e);
                return response(['status'=>'error','message'=>'Failed!']);
            }
        }
            
    }
    public function delete(Request $request){
        $id  = $request->id;
        if(!$id)
            return response(['status'=>'error','message'=>'Remove Failed!']);
        try{
            MainDiscount::find($id)->delete();
            return response(['status'=>'success','message'=>'Remove Successfully!']);
        }
        catch(\Exception $e){
            \Log::info($e);
            return response(['status'=>'error','message'=>'Remove Failed!']);
        }
    }
    public function discountService(Request $request){
        // return $request->service_arr;
        $id = $request->id;
        if($id == 0)
            if( $request->service_arr != "" ){
                $service_arr = $request->service_arr;
            }else{
                $service_arr = [];
            }
            
        else{
            $service_list = MainDiscount::find($id)->service_list;
            $service_arr =explode(';', $service_list);
        }
        $service_arr = MainComboService::whereIn('id',$service_arr)->get();
        return DataTables::of($service_arr)
            ->addColumn('action',function($row){
                return '<a href="javascript:void(0)" class="remove-service" id="'.$row->id.'" title=""><i class="fas fa-trash"></i></a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function saveService(Request $request){
        $service_id = $request->service_id;
        $id = $request->id;
        try{
             $service_info = MainDiscount::find($id);
            if($service_info->service_list == ""){
                $service_info->update(['service_list'=>$service_id]);
                return response(['status'=>'success']);
            }else{
                $service_list = $service_info->service_list;
                $service_arr = explode(';', $service_list);
                if(in_array($service_id, $service_arr))
                    return response(['status'=>'error','message'=>'Service existed!']);
                else{
                    $service_info->update(['service_list'=>$service_list.';'.$service_id]);
                    return response(['status'=>'success']);
                }
            }
        }catch(\Exception $e){
            \Log::info($e);
            return response(['status'=>'error','message'=>'Failed!']);
        }
    }
    function removeService(Request $request){
        $id = $request->id;
        $service_id = $request->service_id;
        if($id == 0)
            return response(['status'=>'error','message'=>'Failed']);
        try{
            $discount_info = MainDiscount::find($id);
            $service_arr = explode(';', $discount_info->service_list);
            if (($key = array_search($service_id, $service_arr)) !== false) {
                unset($service_arr[$key]);
            }
            $service_list = implode(';', $service_arr);
            $discount_info->update(['service_list'=>$service_list]);
            return response(['status'=>'success']);
        }catch(\Exception $e){
            \Log::info($e);
            return response(['status'=>'error','message'=>'Failed']);
        }
    }
}
