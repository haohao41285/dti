<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainFile;
use App\Models\MainUser;

class MainTrackingHistory extends Model
{
    protected $table = "main_tracking_history";
    protected $fillable = [
    	'order_id',
    	'task_id',
    	'subtask_id',
    	'created_by',
    	'created_at',
    	'content',
        'email_list',
        'customer_id',
        'receiver_id'
    ];
    public $timestamps = false;

    public function getFiles(){
    	return $this->hasMany(MainFile::class,'tracking_id','id');
    }
    public function getUserCreated(){
        return $this->belongsTo(MainUser::class,'created_by','user_id')->withDefault();
    }
    public function getReceiver(){
        return $this->belongsTo(MainUser::class,'receiver_id','user_id')->withDefault();
    }
}
