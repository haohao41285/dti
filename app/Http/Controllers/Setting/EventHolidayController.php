<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DataTables;
use App\Models\MainEventHoliday;

class EventHolidayController extends Controller
{
    public function index(){
        return view('event.event');
    }
    public function eventDatatable(Request $request){

        $event_list = MainEventHoliday::all();

        return DataTables::of($event_list)
            ->editColumn('date',function ($row){
                return format_date($row->date);
            })
            ->editColumn('status',function($row){
                if($row->status == 1) $checked='checked';
                else $checked="";
                return '<input type="checkbox" gu_id="'.$row->id.'" gu_status="'.$row->status.'" class="js-switch"'.$checked.'/>';
            })
            ->editColumn('image',function ($row){
                return '<img src="'.asset($row->image).'" style="max-width:150px;"/>';
            })
            ->addColumn('action',function($row){
                return '<a class="btn btn-sm btn-secondary role-edit" ><i class="fas fa-edit"></i></a>';
            })
            ->rawColumns(['action','image','status'])
            ->make(true);
    }
    public function addEvent(Request $request){
        return $request->all();
    }
}
