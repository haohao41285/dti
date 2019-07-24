<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosAppointment
 */
class PosOrder extends BaseModel
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
        'order_booking_id',
        'order_place_id',
        'order_transaction',
        'order_datetime_payment',
        'order_customer_type',
        'order_sent',
        'order_customer_id',
        'order_bill',
        'order_merge_id',
        'order_welcome_id',
        'order_avoid_id',
        'order_paid',
        'order_payment_method',
        'order_promotion_discount',
        'order_coupon_discount',
        'order_coupon_code',
        'order_giftcard_code',
        'order_giftcard_amount',
        'order_card_amount',
        'order_card_number',
        'order_card_type',
        'order_cash_amount',
        'order_drink',
        'order_referal',
        'order_price',
        'order_receipt',
        'order_refdata',
        'order_payback',
        'order_status'
    ];

    protected $guarded = [];

        
}