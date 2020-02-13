<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainTermService extends Model
{
    protected $table = 'main_term_service';
    protected $fillable = [
    	'service_id', // main_combo_service
    	'file_name', // file name for attachment with email
    	'created_by', //main_user
    	'updated_by', //main_user
    	'status'
    ];
}
