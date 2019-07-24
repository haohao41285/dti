<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosCateservice
 */
class PosRemindorder extends BaseModel
{
    protected $table = 'pos_remindorder';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'remindorder_id',
        'remindorder_place_id',
        'remindorder_order_id',
        'remindorder_customer_email',
        'remindorder_customer_phone',
        'remindorder_content',
        'remindorder_datetime',
        'created_at',
        'updated_at',
        'remindorder_status'
    ];

    protected $guarded = [];

        
}