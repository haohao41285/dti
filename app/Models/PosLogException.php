<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosLogException
 */
class PosLogException extends Model
{
    protected $table = 'pos_log_exception';

    public $timestamps = false;

    protected $fillable = [
        'log_id',
        'log_place_id',
        'log_user_id',
        'log_place_name',
        'log_place_phone',
        'log_user_fullname',
        'log_user_phone',
        'log_value_old',
        'log_value_new',
        'log_exception',
        'log_error',
        'created_at',
        'created_by',
        'log_status'
    ];

    protected $guarded = [];

        
}