<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainFile extends Model
{
    protected $table = 'main_file';
    protected $fillable = [
    	'name',
    	'tracking_id',
    	'name_origin',
    	'task_id'
    ];
}
