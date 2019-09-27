<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainFile;

class MainTask extends Model
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
}
