<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosSupply
 */
class PosSupply extends BaseModel
{
    protected $table = 'pos_supply';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'supply_id',
        'supply_name',
        'supply_address',
        'supply_phone',
        'supply_website',
        'supply_description',
        'supply_place_id',
        'created_at',
        'updated_at',
        'supply_status'
    ];

    protected $guarded = [];

        
}