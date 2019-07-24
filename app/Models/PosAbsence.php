<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosAbsence
 */
class PosAbsence extends BaseModel
{
    protected $table = 'pos_absence';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'absence_id',
        'absence_worker_id',
        'absence_place_id',
        'absence_date_start',
        'absence_date_end',
        'absence_time_start',
        'absence_time_end',
        'absence_reason',
        'absence_status'
    ];

    protected $guarded = [];

        
}