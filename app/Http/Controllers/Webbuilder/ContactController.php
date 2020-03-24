<?php

namespace App\Http\Controllers\Webbuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use yajra\Datatables\Datatables;
use App\Models\PosContactCustomer;

class ContactController extends Controller
{
    public function index(Request $request){

        $list_contact = PosContactCustomer::where('cc_place_id',$request->place_id)
                                            ->where('cc_status',1);
        return Datatables::of($list_contact)
            ->editColumn('cc_datetime',function($row){
                return format_datetime($row->cc_datetime);
            })
           ->addColumn('action', function($row){
                return " <a href='javascript:void(0)' class='btn btn-sm btn-secondary delete-contact' id='".$row->cc_id."' data-type='user'><i class='fa fa-trash-o fa-lg'></i></a>" ;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
