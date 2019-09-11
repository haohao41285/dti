<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainSmsSend extends Model
{
    protected $table="main_sms_send";
    protected $fillable= [
    	'sms_send_event_title',
    	'sms_send_event_template_id',
    	'sms_send_event_start_day',
    	'sms_send_event_start_time',
    	'upload_list_receiver',
    	'sms_send_event_status',
    	'sms_total',
    	'sms_send_event_enable',
    	'created_by',
    	'updated_by',
    ];
}
