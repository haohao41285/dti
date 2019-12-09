<?php

namespace App\Http\Controllers\ItTools;

use App\Models\PosPlace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DemoPlaceController extends Controller
{
    public function index(){
        return view('tools.demo-place');
    }
    public function datatable(Request $request){
        $demo_places = PosPlace::where('place_status',1);
        return DataTables::of($demo_places)
            ->editColumn('place_demo',function($row){
                if($row->place_demo  == 1)
                    $check = 'checked';
                else $check = "";
                return '<input type="checkbox" place_demo="'.$row->place_demo.'" place_id="'.$row->place_id.'" class="js-switch"'.$check.'/>';
            })
            ->addColumn('action',function($row){
                return '<a class="btn btn-sm btn-secondary edit-team" href="javascript:void(0)"><i class="fas fa-edit"></i></a>';
            })
            ->rawColumns(['action','place_demo'])
            ->make('true');
    }
    public function changeDemoStatus(Request $request){

        $place_demo = $request->place_demo;
        $place_id = $request->place_id;
        return $request->all();

    }
}
