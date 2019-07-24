<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosAppointment
 */
class PosConvention extends BaseModel
{
    protected $table = 'pos_convention';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'cv_id',
        'cv_place_id',
        'cv_field',
        'cv_name',
        'cv_value',
        'created_at',
        'updated_at',
        'cv_status'
    ];

    protected $guarded = [];

        
}