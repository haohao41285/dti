<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosInputmaterial
 */
class PosInputmaterial extends BaseModel
{
    protected $table = 'pos_inputmaterial';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'inputmaterial_id',
        'inputmaterial_inventory_id',
        'inputmaterial_supply_id',
        'inputmaterial_amount',
        'inputmaterial_cost',
        'inputmaterial_datetime',
        'created_at',
        'updated_at',
        'inputmaterial_place_id'
    ];

    protected $guarded = [];

        
}