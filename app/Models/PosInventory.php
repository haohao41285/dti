<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosInventory
 */
class PosInventory extends BaseModel
{
    protected $table = 'pos_inventory';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'inventory_id',
        'inventory_place_id',
        'inventory_code',
        'inventory_name',
        'inventory_supply_id',
        'inventory_stock',
        'inventory_unit_price',
        'inventory_price',
        'inventory_datetime',
        'inventory_unit',
        'inventory_amount',
        'created_at',
        'updated_at',
        'inventory_status'
    ];

    protected $guarded = [];

        
}