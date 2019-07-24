<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosSpecialday
 */
class PosSpecialday extends BaseModel
{
    protected $table = 'pos_specialday';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'spday_id',
        'spday_place_id',
        'spday_group_specialday',
        'spday_name',
        'spday_date',
        'spday_code',
        'spday_type',
        'spday_discount',
        'spday_listservice',
        'spday_content',
        'created_at',
        'updated_at',
        'spday_status'
    ];

    protected $guarded = [];

        
}