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
        ->addColumn('action',function ($row){
            return '<a class="btn btn-sm btn-delete"  href="javascript:void(0)" title="Delete"><i class="fas fa-trash"></i></a>';
        })
        ->make(true);
    }
}
