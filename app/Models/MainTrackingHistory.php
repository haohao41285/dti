<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainFile;

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
    public $timestamps = false;

    public function getFiles(){
    	return $this->hasMany(MainFile::class,'tracking_id','id');
    }
}
