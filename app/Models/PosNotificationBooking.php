<?php

namespace App\Models;

use App\Models\BaseModel;

class PosNotificationBooking extends BaseModel
{
    protected $table = "pos_notification_booking";

    public static function boot(){
    	parent::boot();
    }

    public $fillable = [
    	'id',
    	'place_id',
    	'booking_id',
    	'checked',
    	'created_by',
    	'updated_by',
    	'created_at',
    	'updated_at'
    ];
}
