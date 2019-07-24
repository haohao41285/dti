<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosSupplyNail
 */
class PosSupplyNail extends BaseModel
{
    protected $table = 'pos_supply_nail';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'sn_id',
        'sn_place_id',
        'sn_name',
        'sn_code',
        'sn_supply_id',
        'sn_image',
        'sn_capacity',
        'sn_unit',
        'sn_type',
        'sn_price',
        'sn_description',
        'sn_datetime',
        'sn_dateexpired',
        'sn_status'
    ];

    protected $guarded = [];

        
}