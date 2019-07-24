<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPromotionAdvertise
 */
class PosPromotionAdvertise extends BaseModel
{
    protected $table = 'pos_promotion_advertise';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'pa_id',
        'pa_place_id',
        'pa_promotion_id',
        'pa_name',
        'pa_time_start',
        'pa_time_end',
        'pa_image',
        'pa_type',
        'pa_status'
    ];

    protected $guarded = [];

        
}