<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosLicenseMacaddress
 */
class PosLicenseMacaddress extends BaseModel
{
    protected $table = 'pos_license_macaddress';

    protected $primaryKey = 'lm_id';

	// public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'lm_id',
        'lm_license',
        'lm_macaddress',
        'lm_status'
    ];

    protected $guarded = [];
        
}