<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCheckin
 */
class PosCheckin extends Model
{
    protected $table = 'pos_checkin';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'checkin_id',
        'checkin_place_id',
        'checkin_worker_id',
        'checkin_ip_address',
        'checkin_reason',
        'checkin_datetime', 
        'created_at',
        'updated_at'
    ];

    protected $guarded = [];

        
}