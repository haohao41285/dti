<?php

namespace App\Http\Controllers\Task;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\GeneralHelper;
use App\Helpers\ImagesHelper;
use App\Models\MainTask;
use App\Models\MainTrackingHistory;
use App\Models\MainFile;
use Carbon\Carbon;
use DataTables;
use Auth;
use Validator;
use DB;

class TaskController extends Controller
{
    public function index(){
    	return view('task.my-task');
    }
    public function myTaskDatatable(Request $request){

    	$task_list = MainTask::where('updated_by',Auth::user()->user_id)->whereNull('task_parent_id');

    	return DataTables::of($task_list)
    		->editColumn('priority',function($row){
    			return GeneralHelper::getPriorityTask()[$row->priority];
    		})
    		->editColumn('status',function($row){
    			return GeneralHelper::getStatusTask()[$row->status];
    		})
    		->addColumn('task',function($row){
    			return '<a href="">#'.$row->id.'</a>';
    		})
    		->editColumn('order_id',function($row){
    			return '<a href="'.route('order-view',$row->order_id).'">#'.$row->order_id.'</a>';
    		})
    		->editColumn('date_start',function($row){
    			if($row->date_start != "")
    				$date_start = Carbon::parse($row->date_start)->format('m/d/Y');
    			else
    				$date_start = "";

    			return $date_start;
    		})
    		->editColumn('date_end',function($row){
    			if($row->date_end != "")
    				$date_end = Carbon::parse($row->date_end)->format('m/d/Y');
    			else
    				$date_end = "";

    			return $date_end;
    		})
    		->editColumn('updated_at',function($row){
    			return Carbon::parse($row->updated_at)->format('m/d/Y h:i A');
    		})
    		->rawColumns(['order_id','task'])
    		->make(true);
    }
    public function postComment(Request $request){
    	// $name = $request->file_image_list[1]->getClientOriginalName();
    	// return $name;
    	$rule = [
    		'order_id' => 'required'
    	];
    	$message = [
    		'order_id.required' => 'Order not exist!'
    	];
    	$validator = Validator::make($request->all(),$rule,$message);
    	if($validator->fails())
    		return response([
    			'status'=>'error',
    			'message' => $validator->getMessageBag()->toArray()
    	]);

    	$order_id = $request->order_id;
    	$task_id = $request->task_id;
    	$content = $request->note;
    	$file_list = $request->file_image_list;
    	$current_month = Carbon::now()->format('m');
    	$file_arr = [];

    	$tracking_arr = [
    		'order_id' => $order_id,
    		'task_id' => $task_id,
    		'content' => $content,
    		'created_by' => Auth::user()->user_id,
    	];
    	DB::beginTransaction();
    	$tracking_create = MainTrackingHistory::create($tracking_arr);

    	foreach ($file_list as $key => $file) {

    		$file_name = ImagesHelper::uploadImage2($file,$current_month);
    		$file_arr[] = [
    			'name' => $file_name,
    			'tracking_id' => $tracking_create->id
    		];

    	}
    	$file_create = MainFile::insert($file_arr);



    	if(!isset($tracking_create) || !isset($file_create))
    	{
    		DB::callback();
    		return response(['status'=>'error', 'message'=> 'Failed!']);
    	}
    	else{
    		DB::commit();
    		return response(['status'=> 'success','message'=>'Successly!']);
    	}

    }
}
