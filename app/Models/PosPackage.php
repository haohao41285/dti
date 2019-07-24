<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPackage
 */
class PosPackage extends BaseModel
{
    protected $table = 'pos_package';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'package_id',
        'package_name',
        'package_listservice_id',
        'package_price',
        'package_image',
        'package_description',
        'package_place_id',
        'created_at',
        'updated_at',
        'package_status'
    ];

    protected $guarded = [];

        
}