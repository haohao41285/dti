<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainEventHoliday;
use App\Helpers\ImagesHelper;
use Validator;
use DataTables;
use Gate;

class EventHolidayController extends Controller
{
    public function index(){

        if(Gate::denies('permission','setup-event-holiday'))
            return doNotPermission();

        return view('event.event');
    }
    public function eventDatatable(Request $request){

        if(Gate::denies('permission','setup-event-holiday'))
            return doNotPermission();

        $event_list = MainEventHoliday::all();

        return DataTables::of($event_list)
            ->editColumn('date',function ($row){
                return format_date($row->date);
            })
            ->editColumn('status',function($row){
                if($row->status == 1) $checked='checked';
                else $checked="";
                return '<input type="checkbox" id="'.$row->id.'" status="'.$row->status.'" class="js-switch"'.$checked.'/>';
            })
            ->editColumn('image',function ($row){
                if($row->image != "")
                    return '<img src="'.asset($row->image).'" style="max-width:150px;"/>';
            })
            ->addColumn('action',function($row){
                return '<a class="btn btn-sm btn-secondary event-delete" title="Delete Event" id="'.$row->id.'"><i class="fas fa-trash-alt"></i></a>';
            })
            ->addColumn('image_hidden',function($row){
                return asset($row->image);
            })
            ->rawColumns(['action','image','status'])
            ->make(true);
    }
    public function addEvent(Request $request){


        if(Gate::denies('permission','setup-event-holiday'))
            return doNotPermission();

        $rule = [
            'date' => 'date_format:m/d/Y',
        ];

        if($request->id > 0){
            $rule['name'] = 'required|unique:main_event_holiday,id,'.$request->id;
        }else{
            $rule['name'] = 'required|unique:main_event_holiday,name';
        }

        $message = [
            'name.required' => 'Enter Name Event',
            'date.date_format' => 'Date not true format'
        ];
        if(isset($request->image)){
            $rule['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
            $message['image.mimes'] = 'Image not true type';
            $message['image.max'] = 'Maximum image 5MB';
        }
        $validator = Validator::make($request->all(),$rule,$message);
        if($validator->fails()){
            return response([
                'status' => 'error',
                'message' => $validator->getMessagebag()->toArray(),
            ]);
        }
        $input = $request->all();
        if($request->image)
             $input['image'] = ImagesHelper::uploadImage2($request->image,date('m'),'images/event/');
        $input['status'] = 1;
        $input['date'] = format_date_db($request->date);

        if($request->id > 0){
            $input['updated_by'] = Auth::user()->user_id;
            $update_event = MainEventHoliday::find($request->id)->update($input);
        }else{
            $input['created_by'] = Auth::user()->user_id;
            $update_event = MainEventHoliday::create($input);
        }

        if(!$update_event)
            return response(['status'=>'error','message'=>'Save event Failed!']);
        else
            return response(['status'=>'success','message'=>'Save event Successfully!']);
    }
    public function deleteEvent(Request $request){

        if(Gate::denies('permission','setup-event-holiday'))
            return doNotPermission();

        $id = $request->id;
        $delete_event = MainEventHoliday::find($id);
        $delete_event = $delete_event->delete();
        if(!$delete_event){
            return response(['status'=>'error','message'=>'Delete Failed!']);
        }else
            return response(['status'=>'success','message'=>'Delete Successfully!']);
    }
    public function changeStatusEvent(Request $request){

        if(Gate::denies('permission','setup-event-holiday'))
            return doNotPermission();

        $id = $request->id;
        $status = $request->status;

        if($status == 1)
            $status_update = 0;
        else
            $status_update = 1;
        $event_update = MainEventHoliday::find($id)->update(['status'=>$status_update]);
        if($event_update)
            return response(['status'=>'success','message'=>'Change Successfully!']);
        else
            return response(['status'=>'error','message'=>'Change Failed!']);
    }
}
