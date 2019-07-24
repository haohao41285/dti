<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPlace
 */
class PosBanner extends BaseModel
{
    protected $table = 'pos_banner';


	public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'ba_id',
        'ba_place_id',
        'ba_name',
        'ba_index',
        'ba_descript',
        'ba_image',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'ba_status'
    ];

    protected $guarded = [];
        
}