<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosCustomer
 */
class PosCustomerLoyalty extends BaseModel
{
    protected $table = 'pos_customer_loyalty';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'cl_id',
        'cl_customer_id',
        'cl_place_id',
        'cl_score',
        'cl_used',
        'cl_status'
    ];

    protected $guarded = [];

        
}