<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\BaseModel;

/**
 * Class PosLogMessage
 */
class PosLogMessage extends Model
{
    protected $table = 'pos_log_message';

    public $timestamps = false;

    /*public static function boot()
    {
        parent::boot();
    }*/

    protected $fillable = [
        'lm_id',
        'lm_place_id',
        'lm_customer_id',
        'lm_customer_phone',
        'lm_date_send',
        'lm_date_income',
        'lm_message_send',
        'lm_message_income',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'lm_status'
    ];

    protected $guarded = [];

        
}