<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class PosCoupon extends Model
{
    protected $table = 'pos_coupon';

    public $timestamps = true;

    protected $fillable = [
        'coupon_id',
        'coupon_place_id',
        'coupon_customer_id',
        'coupon_code',
        'coupon_discount',
        'coupon_type',
        'coupon_quantiy_limit',
        'coupon_quantiy_use',
        'coupon_customer_type',
        'coupon_deadline',
        'coupon_startdate',
        'coupon_list_service',
        'coupon_linkimage',
        'coupon_image_font',
        'coupon_sub_id',
        'created_at',
        'updated_at',
        'coupon_status'
    ];

    protected $guarded = [];

        
}