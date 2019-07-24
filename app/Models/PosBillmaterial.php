<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosBillmaterial
 */
class PosBillmaterial extends BaseModel
{
    protected $table = 'pos_billmaterial';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'billmaterial_id',
        'billmaterial_number',
        'billmaterial_supply_id',
        'billmaterial_amount_money',
        'billmaterial_datetime',
        'billmaterial_place_id',
        'created_at',
        'updated_at',
        'billmaterial_status'
    ];

    protected $guarded = [];

        
}