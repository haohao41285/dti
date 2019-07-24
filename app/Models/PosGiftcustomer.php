<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosGiftcustomer
 */
class PosGiftcustomer extends BaseModel
{
    protected $table = 'pos_giftcustomer';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'gc_id',
        'gc_place_id',
        'gc_name',
        'gc_content',
        'gc_image',
        'created_at',
        'updated_at',
        'gc_status'
    ];

    protected $guarded = [];

        
}