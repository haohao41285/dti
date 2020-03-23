<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainDiscount;
use DataTables;

class SetupDiscount extends Controller
{
    public function index(){
        return view('setting.setup-discount');
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
        try{
            $input['date_start'] = format_date_db($input['date_start']);
            $input['date_end'] = format_date_db($input['date_end']);
            $input['code'] = strtoupper($input['code']);
            MainDiscount::create($input);
            return response(['status'=>'success','message'=>'Successfully!']);
        }
        catch(\Exception $e){
            \Log::info($e);
            return response(['status'=>'error','message'=>'Failed!']);
        }

        
    }
}
