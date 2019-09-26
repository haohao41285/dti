<?php

namespace App\Http\Controllers\Task;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\GeneralHelper;
use App\Helpers\ImagesHelper;
use App\Models\MainTask;
use App\Models\MainTrackingHistory;
use App\Models\MainFile;
use App\Models\MainUser;
use Carbon\Carbon;
use DataTables;
use Auth;
use Validator;
use DB;
use ZipArchive;

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
    			return '<a href="'.route('task-detail',$row->id).'">#'.$row->id.'</a>';
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
    		'order_id' => 'required',
            'note' => 'required'
    	];
    	$message = [
    		'order_id.required' => 'Order not exist!',
            'note.required' => 'Comment Empty!'
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
    		'task_id' => $task_id==0?NULL:$task_id,
    		'content' => $content,
    		'created_by' => Auth::user()->user_id,
    	];
    	DB::beginTransaction();
    	$tracking_create = MainTrackingHistory::create($tracking_arr);

        if($file_list != ""){
            //CHECK SIZE IMAGE
            $size_total = 0;
            foreach ($file_list as $key => $file){
                $size_total += $file->getSize();
            }
            $size_total = number_format($size_total / 1048576, 2); //Convert KB to MB
            if($size_total > 100){
                return response(['status'=>'error','message'=>'Total Size Image maximum 100M!']);
            }
            //Upload Image
            foreach ($file_list as $key => $file) {

                $file_name = ImagesHelper::uploadImage2($file,$current_month);
                $file_arr[] = [
                    'name' => $file_name,
                    'name_origin' => $file->getClientOriginalName(),
                    'tracking_id' => $tracking_create->id,
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
        if(!isset($tracking_create))
        {
            DB::callback();
            return response(['status'=>'error', 'message'=> 'Failed!']);
        }
        else{
            DB::commit();
            return response(['status'=> 'success','message'=>'Successly!']);
        }
        
    	
    }
    public function downImage(Request $request){

        $src_image = $request->src;

        if(file_exists($src_image)){
            return response()->download($src_image);            
        }
        else 
            return back()->with(['error'=>"Download Failed"]);
    }
    public function taskDetail($id){

        $data['task_info'] = MainTask::find($id);
        $data['id'] = $id;
        return view('task.task-detail',$data);
    }
   
    public function taskTracking(Request $request){

        $task_id = $request->task_id;

        $order_tracking = MainUser::join('main_tracking_history',function($join){
            $join->on('main_tracking_history.created_by','main_user.user_id');
        })
            ->where('main_tracking_history.task_id',$task_id)
            ->whereNull('main_tracking_history.subtask_id')
            ->select('main_tracking_history.*','main_user.user_firstname','main_user.user_lastname','main_user.user_team','main_user.user_nickname')->get();

        return DataTables::of($order_tracking)

            ->addColumn('user_info',function($row){
                return '<span>'.$row->user_nickname.'('.$row->getFullname().')</span><br>
                        <span>'.Carbon::parse($row->created_at)->format('m/d/Y h:i A').'</span><br>
                        <span class="badge badge-secondary">'.$row->getTeam->team_name.'</span>';
            })
            ->addColumn('task',function($row){
                return "<a href='' >Task#".$row->task_id."</a>";
            })
            ->editColumn('content',function($row){
                $file_list = MainFile::where('tracking_id',$row->id)->get();
                $file_name = "<div class='row '>";
                if($file_list->count() > 0 ){

                    foreach ($file_list as $key => $file) {
                        $zip = new ZipArchive();

                        if ($zip->open($file->name, ZipArchive::CREATE) !== TRUE) {
                            $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><img class="file-comment ml-2" src="'.asset($file->name).'"/></form>';
                        }else{
                            $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>'.$file->name_origin.'</a></form>';
                        }
                    }
                }
                $file_name .= "</div>";
                return $row->content."<br>".$file_name;
            })
            ->rawColumns(['user_info','task','content'])
            ->make(true);
    }
    public function taskAdd($id = 0){
        $data['user_list'] = MainUser::where('user_id',"!=",Auth::user()->user_id)->get();
        return view('task.add-task',$data);
    }
}
