<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosCustomer
 */
class PosCustomerBehaviour extends BaseModel
{
    protected $table = 'pos_customer_behaviour';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'cb_id',
        'cb_place_id',
        'cb_customer_id',
        'cb_order_id',
        'cb_content',
        'created_at',
        'updated_at',
        'cb_status'
    ];

    protected $guarded = [];

        
}