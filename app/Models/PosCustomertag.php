<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosCustomertag
 */
class PosCustomertag extends BaseModel
{
    protected $table = 'pos_customertag';

    protected $primaryKey = 'customertag_id';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'customertag_id',
        'customertag_place_id',
        'customertag_name',
        'customertag_description',
        'customertag_rule_chargedup',
        'customertag_rule_months',
        'customertag_status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $guarded = [];

        
}