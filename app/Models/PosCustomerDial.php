<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosCustomerDial
 */
class PosCustomerDial extends BaseModel
{
    protected $table = 'pos_customer_dial';

    public $timestamps = true;

    protected $fillable = [
        'cd_id',
        'cd_place_id',
        'cd_fullname',
        'cd_phone',
        'cd_datetime',
        'cd_description',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'cd_status'
    ];

    protected $guarded = [];

        
}