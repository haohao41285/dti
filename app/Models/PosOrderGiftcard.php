<?php

namespace App\Models;

use App\Models\BaseModel;

class PosOrderGiftcard extends BaseModel
{
    protected $table = 'pos_order_giftcard';
    public $timestamps = true;
    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'og_id',
        'og_place_id',
        'og_order_id',
        'og_giftcard_code',
        'og_giftcard_id',
        'og_datetime',
        'og_price',
        'og_status'
    ];

    protected $guarded = [];       
}