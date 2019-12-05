<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PosBookingDetail extends Model
{
    public $timestamps = false;
    protected $table = "pos_booking_details";
    protected $fillable = [
    	'booking_code',
    	'bookingdetail_place_id',
    	'service_id',
    	'worker_id',
    	'booking_time'
    ];
}