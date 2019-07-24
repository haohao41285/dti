<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class MainMkt
 */
class MainMkt extends BaseModel
{
    protected $table = 'main_mkt';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'mkt_id',
        'mkt_place_id',
        'mkt_tool_id',
        'mkt_quantity',
        'mkt_content',
        'mkt_price_per_unit',
        'mkt_is_auto',
        'created_at',
        'created_by'
    ];

    protected $guarded = [];

        
}