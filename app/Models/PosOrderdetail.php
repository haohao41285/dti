<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosAppointmentdetail
 */
class PosOrderdetail extends BaseModel
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
        'orderdetail_packagedetail_id',
        'orderdetail_datetime',
        'orderdetail_place_id',
        'orderdetail_extra',
        'orderdetail_tip',
        'orderdetail_price',
        'orderdetail_price_hold',
        'orderdetail_order_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $guarded = [];

        
}