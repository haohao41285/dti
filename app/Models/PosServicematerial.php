<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosServicematerial
 */
class PosServicematerial extends BaseModel
{
    protected $table = 'pos_servicematerial';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'servicematerial_id',
        'servicematerial_inventory_id',
        'servicematerial_service_id',
        'servicematerial_amount',
        'servicematerial_place_id',
        'created_at',
        'updated_at',
        'servicematerial_status'
    ];

    protected $guarded = [];

        
}