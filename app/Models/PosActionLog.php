<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosAppointment
 */
class PosActionLog extends Model
{
    protected $table = 'pos_action_log';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'log_id',
        'log_place_id',
        'log_user_id',
        'log_action',
        'log_value',
        'created_at',
        'updated_at',
        'log_status'
    ];
    protected $guarded = [];
}