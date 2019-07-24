<?php

namespace App\Models;

use App\Models\BaseModel;

class PosOrderSupplyNail extends BaseModel
{
    protected $table = 'pos_order_supply_nail';
    public $timestamps = true;
    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'os_id',
        'os_place_id',
        'os_order_id',
        'os_supply_nail_id',
        'os_price',
        'os_name',
        'os_quantity',
        'os_status'
    ];

    protected $guarded = [];       
}