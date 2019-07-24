<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\BaseModel;

/**
 * Class PosLoginLog
 */
class PosLoginLog extends Model
{
    protected $table = 'pos_login_log';

    public $timestamps = false;

    /*public static function boot()
    {
        parent::boot();
    }*/

    protected $fillable = [
        'log_id',
        'log_place_id',
        'log_user_id',
        'log_user_fullname',
        'log_user_email',
        'log_user_phone',
        'log_place_name',
        'log_datetime',
        'log_url',
        'log_type',
        'log_status'
    ];

    protected $guarded = [];

        
}