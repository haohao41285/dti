<?php

namespace App\Http\Controllers\Task;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Helpers\GeneralHelper;
use App\Helpers\ImagesHelper;
use App\Models\MainTask;
use App\Models\MainTrackingHistory;
use App\Models\MainFile;
use App\Models\MainUser;
use App\Models\MainTeam;
use Carbon\Carbon;
use App\Jobs\SendNotification;
use DataTables;
use Auth;
use Validator;
use DB;
use ZipArchive;
use Laracasts\Presenter\PresentableTrait;

class TaskController extends Controller
{
    use PresentableTrait;
    protected $presenter = 'App\\Presenters\\ThemeMailPresenter';
    public function index(){
    	return view('task.my-task');
    }
    public function myTaskDatatable(Request $request){

    	$task_list = MainTask::where('updated_by',Auth::user()->user_id)->whereNull('task_parent_id');

    	return DataTables::of($task_list)
    		->editColumn('priority',function($row){
    			return getPriorityTask()[$row->priority];
    		})
    		->editColumn('status',function($row){
    			return getStatusTask()[$row->status];
    		})
    		->addColumn('task',function($row){
    		    if(count($row->getSubTask) >0){
    		        $detail_button = "<i class=\"fas fa-plus-circle details-control text-danger\" id='".$row->id."'></i>";
                }else $detail_button = "";

    			return $detail_button.'<a href="'.route('task-detail',$row->id).'"> #'.$row->id.'</a>';
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
            ->editColumn('category',function($row){
                return getCategory()[$row->category];
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
            'email_list' => $request->email_list
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

                $file_name = ImagesHelper::uploadImage2($file,$current_month,'images/comment/');
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
        $data['team'] = MainTeam::all();

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
                        <span>'.format_datetime($row->created_at).'</span><br>
                        <span class="badge badge-secondary">'.$row->getTeam->team_name.'</span>';
            })
            ->addColumn('task',function($row){
                return "<a href='' >Task#".$row->task_id."</a>";
            })
            ->editColumn('content',function($row){
                $file_list = $row->getFiles;
                $file_name = "<div class='row '>";

                    foreach ($file_list as $key => $file) {
                        $zip = new ZipArchive();

                        if ($zip->open($file->name, ZipArchive::CREATE) !== TRUE) {
                            $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><img class="file-comment ml-2" src="'.asset($file->name).'"/></form>';
                        }else{
                            $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>'.$file->name_origin.'</a></form>';
                        }
                    }

                $file_name .= "</div>";
                return $row->content."<br>".$file_name;
            })
            ->rawColumns(['user_info','task','content'])
            ->make(true);
    }
    public function taskAdd($id = 0){

        $data['user_list'] = MainUser::all();
        $data['task_parent_id'] = $id;
         $data['task_name'] = "";

        if($id>0){
            $data['task_name'] = MainTask::find($id)->subject;
        }
        return view('task.add-task',$data);
    }
    public function getTask(Request $request){

        $task_parent_id = $request->task_parent_id;

        $task_name = MainTask::find($task_parent_id);
        if(!isset($task_name))
            return response(['status'=>'error','message'=>'ID Task Correctly!']);
        else{
            if($task_name == "")
                return response(['status'=>'error','message'=>'ID Task Correctly!']);
            else{
                $task_name = strtoupper($task_name->subject);
                return response(['task_name'=>$task_name]);
            }
        }
    }
    public function saveTask(Request $request){
        // return $request->all();

        $subject = $request->subject;

        if($subject == ""){
            return back()->with(['error'=>'Enter Subject, Please!']);
        }

        $input =  $request->all();

        if(!isset($request->id)){

            $input['created_by'] = Auth::user()->user_id;
            $input['updated_by'] = Auth::user()->user_id;
            $task_save = MainTask::create($input);

        }else{
            //UPDATE TASK
            $input['updated_by'] = Auth::user()->user_id;
            $task_info = MainTask::find($request->id);
            $task_save = $task_info->update($input);

            //ADD TRACKING HISTORY
            $task_tracking = [
                'order_id' => $task_info->order_id,
                'task_id' => $request->id,
                'created_by' => Auth::user()->user_id,
                'content' => $request->note,
            ];
            $tracking_history = MainTrackingHistory::find($task_info->order_id)->create($task_tracking);

            if(!isset($task_save) || !isset($tracking_history))
                return back()->with(['error'=>'Save Error. Check Again, Please!']);
            else
                return redirect()->route('my-task');
        }

        if(!isset($task_save))
            return back()->with(['error'=>'Save Error. Check Again, Please!']);
        else
            return redirect()->route('my-task');
    }
    public function getSubtask(Request $request){

        $task_id = $request->task_id;

        $subtask_list = MainTask::where('task_parent_id',$task_id);

        return DataTables::of($subtask_list)
            ->editColumn('priority',function($row){
                return getPriorityTask()[$row->priority];
            })
            ->editColumn('status',function($row){
                return getStatusTask()[$row->status];
            })
            ->addColumn('task',function($row){
                return '<a href="'.route('task-detail',$row->id).'">#'.$row->id.'</a>';
            })
            ->editColumn('order_id',function($row){
                return '<a href="'.route('order-view',$row->order_id).'">#'.$row->order_id.'</a>';
            })
            ->addColumn('assign_to',function ($row){
                return $row->getAssignTo->user_nickname;
            })
            ->editColumn('category',function($row){
                return getCategory()[$row->category];
            })
            ->editColumn('date_start',function($row){
                if($row->date_start != "")
                    $date_start = format_date($row->date_start);
                else
                    $date_start = "";

                return $date_start;
            })
            ->editColumn('assign_to',function($row){
                return $row->getUser->user_nickname;
            })
            ->editColumn('date_end',function($row){
                if($row->date_end != "")
                    $date_end = format_date($row->date_end);
                else
                    $date_end = "";

                return $date_end;
            })
            ->editColumn('updated_at',function($row){
                return format_datetime($row->updated_at)." by ".$row->getUpdatedBy->user_nickname;
            })
            ->rawColumns(['order_id','task'])
            ->make(true);
    }
    public function editTask($id){

        $data['user_list'] = MainUser::all();

        $data['task_info'] = MainTask::find($id);

        $data['id'] = $id;

        $data['task_name'] = $data['task_info']->subject;

        return view('task.edit-task',$data);
    }
    public function sendMailNotification(Request $request){
        // return public_path('invoice9267054355559.pdf');

        $rule = [
            'subject' => 'required',
            'message' => 'required',
        ];
        $message = [
            'subject.required' => 'Type Subject',
            'message.required' => 'Type Message',
        ];
        $validator = Validator::make($request->all(),$rule,$message);
        if($validator->fails())
            return response([
                'status' => 'error',
                'message' => $validator->getMessageBag()->toArray()
            ]);
        //GET EMAIL TEAM
        $team_info =  MainTeam::find($request->team);
        $team_email = $team_info->team_email;
        $team_name = $team_info->team_name;


        if(!isset($team_email))
            return response(['status'=>'error','message'=>'Get Email Team Error!']);
        else{
            if($team_email == "")
                return response(['status'=>'error','message'=>'Get Email Team Error!']);
            else{
                // $input =  MainTeam::find($request->team);
                $input['email'] = $team_email;
                $input['name'] = $team_name;
                $input['subject'] = $request->subject;
                $input['message'] = $request->message;
                dispatch(new SendNotification($input));
                return response(['status'=>'success','message'=>'Message has been sent']);
            }
        }
    }

}
