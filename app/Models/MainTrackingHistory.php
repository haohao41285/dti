<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainTrackingHistory extends Model
{
    protected $table = "main_tracking_history";
    protected $fillable = [
    	'order_id',
    	'task_id',
    	'subtask_id',
    	'created_by',
    	'created_at',
    	'content'
    ];
}
