<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosBillcustomer
 */
class PosBillcustomer extends BaseModel
{
    protected $table = 'pos_billcustomer';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'billcustomer_id',
        'billcustomer_ticket',
        'billcustomer_customer_id',
        'billcustomer_datetime',
        'billcustomer_appointment_id',
        'billcustomer_extra',
        'billcustomer_tip',
        'billcustomer_discount',
        'billcustomer_place_id',
        'created_at',
        'updated_at',
        'billcustomer_status'
    ];

    protected $guarded = [];

        
}