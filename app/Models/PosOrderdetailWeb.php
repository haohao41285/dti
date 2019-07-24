<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosAppointmentdetail
 */
class PosOrderdetailWeb extends Model
{
    protected $table = 'pos_orderdetail';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'orderdetail_id',
        'orderdetail_worker_id',
        'orderdetail_service_id',
        'orderdetail_package_id',
        'orderdetail_datetime',
        'orderdetail_place_id',
        'orderdetail_extra',
        'orderdetail_tip',
        'orderdetail_price',
        'orderdetail_order_id'
    ];

    protected $guarded = [];

        
}