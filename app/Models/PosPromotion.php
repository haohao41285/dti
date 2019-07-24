<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPromotion
 */
class PosPromotion extends BaseModel
{
    protected $table = 'pos_promotion';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'promotion_id',
        'promotion_name',
        'promotion_image',
        'promotion_date_start',
        'promotion_date_end',
        'promotion_time_start',
        'promotion_time_end',
        'promotion_description',
        'promotion_listservice_id',
        'promotion_place_id',
        'promotion_discount',
        'promotion_type',
        'promotion_status'
    ];

    protected $guarded = [];

        
}