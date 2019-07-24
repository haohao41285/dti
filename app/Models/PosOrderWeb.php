<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosAppointment
 */
class PosOrderWeb extends Model
{
    protected $table = 'pos_order';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'order_id',
        'order_image',
        'order_promotion_id',
        'order_place_id',
        'order_transaction',
        'order_datetime',
        'order_customer_id',
        'order_bill',
        'order_merge_id',
        'order_paid',
        'order_price',
        'order_receipt',
        'order_payback',
        'order_status'
    ];

    protected $guarded = [];

        
}