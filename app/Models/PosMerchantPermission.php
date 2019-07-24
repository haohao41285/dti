<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosUsergroup
 */
class PosMerchantPermission extends BaseModel
{
    protected $table = 'pos_merchant_permission';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'mp_id',
        'mp_name',
        'mp_display_name',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $guarded = [];

        
}