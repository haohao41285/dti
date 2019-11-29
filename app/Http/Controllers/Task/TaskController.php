<?php

namespace App\Http\Controllers\Task;

use App\Models\MainComboService;
use App\Models\MainGroupUser;
use App\Models\MainPermissionDti;
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
use App\Models\MainNotification;
use Gate;

class TaskController extends Controller
{
    use PresentableTrait;
    protected $presenter = 'App\\Presenters\\ThemeMailPresenter';

    public function index(){
        if(Gate::denies('permission','my-task-read'))
            return doNotPermission();

        $data['user_list'] = MainUser::active()->get();
        $data['service_list'] = MainComboService::where([['cs_type',2],['cs_status',1]])->get();
    	return view('task.my-task',$data);
    }
    public function myTaskDatatable(Request $request){

        if(Gate::denies('permission','my-task-read'))
            return doNotPermission();

        $task_list = MainTask::where('updated_by',Auth::user()->user_id)->whereNull('task_parent_id');
        if($request->category != "")
            $task_list->where('category',$request->category);
        if($request->service_id != "")
            $task_list->where('service_id',$request->service_id);
        if($request->assign_to && $request->assign_to != ""){
            $assign_to = $request->assign_to;
            $task_list->where(function($query) use($assign_to){
                $query->where('assign_to',$assign_to)
                    ->orWhere('assign_to','like','%;'.$assign_to)
                    ->orWhere('assign_to','like','%;'.$assign_to.';%')
                    ->orWhere('assign_to','like',$assign_to.';%');
            });
        }
        if($request->priority != "")
            $task_list->where('priority',$request->priority);
        if($request->status != "")
            $task_list->where('status',$request->status);
        if(isset($request->task_dashboard)){
            $task_list->where('status','!=',3);
        }
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

    			return $detail_button.'&nbsp&nbsp<a href="'.route('task-detail',$row->id).'"> #'.$row->id.'</a>';
    		})
    		->editColumn('order_id',function($row){
    		    if($row->order_id != null)
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
            ->editColumn('complete_percent',function($row){
                if(!empty($row->complete_percent))
                    return $row->complete_percent."%";
            })
    		->rawColumns(['order_id','task'])
    		->make(true);
    }
    public function postComment(Request $request){
//        return $request->all();
    	$rule = [
    		'order_id' => 'required',
            'note' => 'required'
    	];
    	$message = [
    		'order_id.required' => 'Order not exist!',
            'note.required' => 'Comment required!'
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
            'email_list' => $request->email_list,
            'receiver_id' => $request->receiver_id,
    	];
    	DB::beginTransaction();
    	$tracking_create = MainTrackingHistory::create($tracking_arr);
    	//CHANGE STATUS
        $change_status_task = 'ok';
        if(isset($request->status))
            $change_status_task = MainTask::find($task_id)->update(['status'=>$request->status]);

    	//SAVE NOTIFICATION
        $task_id = "";
        if($tracking_create->task_id != "")
            $task_id = $tracking_create->task_id;
        if($tracking_create->subtask_id != "")
            $task_id = $tracking_create->subtask_id;

        $content = $tracking_create->getUserCreated->user_nickname." created a comment on task #".$task_id;

        $notification_arr = [
            'content' => $content,
            'href_to' => route('task-detail',$task_id),
            'receiver_id' => $tracking_create->receiver_id,
            'read_not' => 0,
            'created_by' => Auth::user()->user_id,
        ];
        $notification_create = MainNotification::create($notification_arr);

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

            if(!isset($tracking_create) || !isset($file_create) || !isset($notification_create) || !isset($change_status_task))
            {
                DB::callback();
                return response(['status'=>'error', 'message'=> 'Failed!']);
            }
            else{
                DB::commit();
                return response(['status'=> 'success','message'=>'Successly!']);
            }
        }
        if(!isset($tracking_create) || !isset($notification_create))
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
        $assign_to_arr = explode(';',$data['task_info']->assign_to);
        $data['assign_to'] = MainUser::whereIn('user_id',$assign_to_arr)->get();
        $data['team'] = MainTeam::all();

        return view('task.task-detail',$data);
    }

    public function taskTracking(Request $request){

        $task_id = $request->task_id;
        $task_id = 93;

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
                $file_list = MainFile::where('tracking_id',$row->id)->get();
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

        if(Gate::denies('permission','create-new-task'))
            return doNotPermission();

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
//        return $request->all();
        if(Gate::denies('permission','create-new-task'))
            return doNotPermission();

        $subject = $request->subject;

        if($subject == ""){
            return back()->with(['error'=>'Enter Subject, Please!']);
        }

        $input =  $request->all();
        if($request->date_start != "")
            $input['date_start'] = format_date_db($request->date_start);
        if($request->date_end != "")
            $input['date_end'] = format_date_db($request->date_end);

        if(!isset($request->id)){

            $input['created_by'] = Auth::user()->user_id;
            $input['updated_by'] = Auth::user()->user_id;
            $task_save = MainTask::create($input);
            //SAVE NOTIFICATION
            $content = Auth::user()->user_nickname. "created a task #".$task_save->id;
            $notification_arr = [
                'content' => $content,
                'href_to' => route('task-detail',$task_save->id),
                'receiver_id' => $request->assign_to,
                'read_not' => 0,
                'created_by' => Auth::user()->user_id,
            ];
            $notification_create = MainNotification::create($notification_arr);

        }else{

            $task_info = MainTask::find($request->id);

            //PARENT TASK
            $subTaskList = $task_info->getSubTask;

            //CHILD TASK
            if(!empty($task_info->task_parent_id)){
                $parent_task = MainTask::find($task_info->task_parent_id);
                $subTaskList = $parent_task->getSubTask->where('id','!=',$request->id);

            }
            $subTaskTotal = count($subTaskList);

            $complete_percent_sub = 0;
            if( $subTaskTotal > 0 ){
                $complete_percent_total = 0;
                foreach($subTaskList as $subTask){
                    $complete_percent_total += $subTask->complete_percent;
                }
                $complete_percent_sub = $complete_percent_total/$subTaskTotal;
            }


            //GET STATUS TASK FOLLOW PERCENT COMPLETE
            if(!empty($task_info->task_parent_id)){
                //UPDATE PARENT TASK
                if($complete_percent_sub > 0)
                    $parentPercent = ($parent_task->complete_percent + $complete_percent_sub + $input['complete_percent']) / 3;
                else
                    $parentPercent = ($parent_task->complete_percent + $input['complete_percent']) / 2;

                if($parentPercent == 0)
                    $status = 1;
                elseif($parentPercent > 0 && $parentPercent < 100)
                    $status = 2;
                else $status = 3;

                $parent_task->update(['complete_percent'=>$parentPercent,'status'=>$status]);

            }else{

                $input['complete_percent'] +=  $complete_percent_sub;
                if($complete_percent_sub > 0)
                    $input['complete_percent'] = $input['complete_percent']/2;
            }


            if($input['complete_percent'] == 0)
                $input['status'] = 1;
            elseif($input['complete_percent'] > 0 && $input['complete_percent'] < 100)
                $input['status'] = 2;
            else $input['status'] = 3;
            //UPDATE TASK
            $input['updated_by'] = Auth::user()->user_id;
            $task_save = $task_info->update($input);

            //ADD TRACKING HISTORY
            $task_tracking = [
                'order_id' => $task_info->order_id,
                'task_id' => $request->id,
                'created_by' => Auth::user()->user_id,
                'content' => $request->note,
            ];
            $tracking_history = MainTrackingHistory::create($task_tracking);

            //SAVE NOTIFICATION
            $content = Auth::user()->user_nickname. " updated a task #".$request->id;
            $notification_arr = [
                'content' => $content,
                'href_to' => route('task-detail',$request->id),
                'receiver_id' => $request->assign_to,
                'read_not' => 0,
                'created_by' => Auth::user()->user_id,
            ];
            $notification_create = MainNotification::create($notification_arr);

            if(!isset($task_save) || !isset($tracking_history) || !isset($notification_create))
                return back()->with(['error'=>'Save Error. Check Again, Please!']);
            else
                return redirect()->route('my-task');
        }


        if(!isset($task_save) || !isset($notification_create))
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
            ->editColumn('complete_percent',function ($row){
                if(!empty($row->complete_percent))
                    return $row->complete_percent."%";
            })
            ->rawColumns(['order_id','task'])
            ->make(true);
    }
    public function editTask($id){

        if(Gate::denies('permission','task-update'))
            return doNotPermission();

        $data['user_list'] = MainUser::all();

        $data['task_info'] = MainTask::find($id);

        $data['id'] = $id;

        $data['task_name'] = $data['task_info']->subject;

        return view('task.edit-task',$data);
    }
    public function sendMailNotification(Request $request){

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
    public function allTask(){

        if(Gate::denies('permission','all-task-read'))
            return doNotPermission();

        $data['user_list'] = MainUser::active()->get();
        $data['service_list'] = MainComboService::where([['cs_type',2],['cs_status',1]])->get();
        return view('task.all-task',$data);
    }
    public function allTaskDatatable(Request $request){

        if(Gate::denies('permission','all-task-read'))
            return doNotPermission();

        if(Auth::user()->user_group_id == 1)
            $task_list = MainTask::whereNull('task_parent_id');
        else
            $task_list = MainTask::where('updated_by',Auth::user()->user_id)->whereNull('task_parent_id');

        if($request->category != "")
            $task_list->where('category',$request->category);
        if($request->service_id != "")
            $task_list->where('service_id',$request->service_id);
        if($request->assign_to && $request->assign_to != ""){
           $assign_to = $request->assign_to;
            $task_list->where(function ($query) use ($assign_to){
                $query->where('assign_to',$assign_to)
                    ->orWhere('assign_to','like','%;'.$assign_to)
                    ->orWhere('assign_to','like','%;'.$assign_to.';%')
                    ->orWhere('assign_to','like',$assign_to.';%');
            });
        }
        if($request->priority != "")
            $task_list->where('priority',$request->priority);
        if($request->status != "")
            $task_list->where('status',$request->status);

        return DataTables::of($task_list)
            ->editColumn('priority',function($row){
                return getPriorityTask()[$row->priority];
            })
            ->editColumn('status',function($row){
                return getStatusTask()[$row->status];
            })
            ->addColumn('task',function($row){
                if(count($row->getSubTask) >0){
                    $detail_button = "<i class='fas fa-plus-circle details-control text-danger' id='".$row->id."'></i>";
                }else $detail_button = "";

                return $detail_button.'&nbsp&nbsp<a href="'.route('task-detail',$row->id).'"> #'.$row->id.'</a>';
            })
            ->editColumn('order_id',function($row){
                if($row->order_id != null)
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
            ->editColumn('complete_percent',function($row){
                if(!empty($row->complete_percent))
                    return $row->complete_percent."%";
            })
            ->editColumn('updated_at',function($row){
                return Carbon::parse($row->updated_at)->format('m/d/Y h:i A');
            })
            ->rawColumns(['order_id','task'])
            ->make(true);
    }
    public function cskhTask($id = 0){
        if(Gate::denies('permission','cskh-task'))
            return doNotPermission();

        $role_arr = [];

        $permission_id = MainPermissionDti::where('permission_slug','cskh-task-read')->first()->id;

        $role_list = MainGroupUser::active()
            ->where('gu_id','!=',1)
            ->where(function ($query){
                $query->where('gu_role_new','!=',null)
                    ->orWhere('gu_role_new','!=','');
            })
            ->select('gu_role_new','gu_id')
            ->get();

        foreach ($role_list as $role){
            $permission_list = explode(';',$role->gu_role_new);
            if(in_array($permission_id,$permission_list)){
                $role_arr[] = $role->gu_id;
            }
        }
        $data['user_list'] = MainUser::active()->whereIn('user_group_id',$role_arr)->get();
        $data['task_parent_id'] = $id;
        $data['task_name'] = "";

        if($id>0){
            $data['task_name'] = MainTask::find($id)->subject;
        }
        return view('task.cskh-task',$data);
    }
    public function getStatusTaskOrder(Request $request){
        $order_id = $request->order_id;
        $task_id = $request->task_id;

        $task_info = MainTask::find($task_id)->status;
        $task_status = getStatusTask()[$task_info];
//        $order_info = Main
    }

}
