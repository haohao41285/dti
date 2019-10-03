<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\PosPlace;
use DataTables;

class PlaceController extends Controller
{
    public function index(){
        return view('tools.place');
    }

    public function datatable(){
        $places = PosPlace::select('place_id','place_name','place_address','place_email','place_phone','place_ip_license','created_at')
            ->where('place_status',1)
            ->get();

        return DataTables::of($places)
        ->editColumn('action',function($places){
            return '<a class="btn btn-sm btn-secondary view" data-id="'.$places->place_id.'" href="#"><i class="fas fa-eye"></i></a>';
        })
        ->editColumn('created_at',function($places){
            return format_datetime($places->created_at);
        })
        ->rawColumns(['action'])
        ->make(true);
    }
}