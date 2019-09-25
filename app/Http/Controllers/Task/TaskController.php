<?php

namespace App\Http\Controllers\Task;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainTask;
use App\Helpers\GeneralHelper;
use DataTables;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(){
    	return view('task.my-task');
    }
    public function myTaskDatatable(Request $request){

    	$task_list = MainTask::whereNull('task_parent_id');

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
    			return '<a href="">#'.$row->order_id.'</a>';
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
}
