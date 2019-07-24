<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosGiftcodelevel
 */
class PosGiftcodelevel extends BaseModel
{
    protected $table = 'pos_giftcodelevel';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'giftcodelevel_id',
        'giftcodelevel_name',
        'giftcodelevel_discount',
        'giftcodelevel_price',
        'giftcodelevel_use_times',
        'giftcodelevel_type',
        'giftcodelevel_description',
        'giftcodelevel_place_id',
        'created_at',
        'updated_at',
        'giftcodelevel_status'
    ];

    protected $guarded = [];

        
}