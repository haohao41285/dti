<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use App\Models\MainFile;
use Auth;
use App\Models\BaseModel;
use Gate;

class MainTask extends BaseModel
{
    protected $table = "main_task";

    protected $fillable = [
    	'subject',
    	'priority',
    	'status',
    	'date_start',
    	'date_end',
    	'complete_percent',
    	'assign_to',
    	'task_parent_id',
    	'order_id',
    	'created_by',
    	'updated_by',
    	'created_at',
    	'updated_at',
    	'service_id',
    	'content',
    	'category',
        'desription',
        'note',
        'place_id'
    ];
    public function getUser(){
    	return $this->belongsTo(MainUser::class,'assign_to','user_id');
    }
    public function getCreatedBy(){
        return $this->belongsTo(MainUser::class,'created_by','user_id');
    }
    public function getUpdatedBy(){
        return $this->belongsTo(MainUser::class,'updated_by','user_id');
    }
    public function getFiles(){
        return $this->hasMany(MainFile::class,'task_id','id');
    }
    public function getService(){
        return $this->belongsTo(MainComboService::class,'service_id','id');
    }
    public function getPlace(){
        return $this->belongsTo(PosPlace::class,'place_id','place_id');
    }
    public function getAssignTo(){
        return $this->belongsTo(MainUser::class,'assign_to','user_id');
    }
    public function getSubTask(){
        return $this->hasMany(MainTask::class,'task_parent_id','id');
    }
    public function getOrder(){
        return $this->belongsTo(MainComboServiceBought::class,'order_id','id');
    }

    public static function getListPendingTasks(){

        if(Gate::allows('permission','dashboard-admin')){
            return self::where('complete_percent','!=',"100");
        }
        elseif(Gate::allows('permission','dashboard-leader')){
            //GET USER OF TEAM
            $users = MainUser::where('user_team',Auth::user()->user_team)->get();
            $task_list = [];
            foreach ($users as $key => $user) {
                $tasks = MainTask::where('complete_percent','!=',"100")->where(function($query) use ($user) {
                    $query->where('assign_to',$user->user_id)
                    ->orWhere('assign_to','LIKE','%;'.$user->user_id)
                    ->orWhere('assign_to','LIKE','%;'.$user->user_id.';%')
                    ->orWhere('assign_to','LIKE',$user->user_id.';%');
                })->get();
                foreach ($tasks as $key => $task) {
                    $task_list[] = $task;
                }
            }
            return array_unique($task_list);
        }
        else{
            return self::where('complete_percent','!=',"100")
                        ->where(function($query) {
                            $query->where('assign_to',Auth::user()->user_id)
                            ->orWhere('assign_to','LIKE','%;'.Auth::user()->user_id)
                            ->orWhere('assign_to','LIKE','%;'.Auth::user()->user_id.';%')
                            ->orWhere('assign_to','LIKE',Auth::user()->user_id.';%');
                        });
        }
    }
    public static function getPendingTasks(){

        $task_list = self::getListPendingTasks();

        if(Gate::allows('permission','dashboard-admin'))
            return $task_list->count();
        elseif(Gate::allows('permission','dashboard-leader'))
            return count($task_list);
        else
            return $task_list->count();
    }
}
