<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    	'update_by',
    	'created_at',
    	'updated_at',
    	'service_id',
    	'content',
    	'category'
    ];
    public function getUser(){
    	return $this->belongsTo(MainUser::class,'assign_to','user_id');
    }
}
