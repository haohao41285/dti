<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosCustomer
 */
class PosPlaceScore extends BaseModel
{
    protected $table = 'pos_place_score';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'ps_id',
        'ps_place_id',
        'ps_name',
        'ps_alias',
        'ps_limit_min',
        'ps_limit_max',
        'ps_value',
        'ps_description',
        'ps_status'
    ];

    protected $guarded = [];

        
}