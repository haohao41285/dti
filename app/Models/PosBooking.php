<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosBooking
 */
class PosBooking extends Model
{
    protected $table = 'pos_booking';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();

        static::creating(function($model){
            $model->created_at = date("Y-m-d H:i:s");
            $model->updated_at = date("Y-m-d H:i:s");
        });

        static::updating(function($model){
            $model->updated_at = date("Y-m-d H:i:s");
        });
    }

    protected $fillable = [
        'booking_id',
        'booking_place_id',
        'booking_customer_id',
        'booking_lstservice',
        'booking_time_selected',
        'booking_worker_id',
        'booking_ip',
        'created_at',
        'updated_at',
        'booking_redirect_url',
        'booking_verify_code',
        'booking_status',
        'booking_type'
    ];

    protected $guarded = [];

        
}