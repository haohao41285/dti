<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosService
 */
class PosServiceWebsite extends BaseModel
{
    protected $table = 'pos_service_website';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'service_id',
        'service_name',
        'service_cate_id',
        'service_place_id',
        'service_posservice_id',
        'service_tag',
        'service_short_name',
        'service_duration',
        'service_price',
        'service_price_extra',
        'service_price_repair',
        'service_updown',
        'service_image',
        'service_description',
        'service_descript_website',
        'created_at',
        'updated_at',
        'service_status'
    ];

    protected $guarded = [];

        
}