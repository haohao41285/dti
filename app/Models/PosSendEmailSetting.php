<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosSendEmailSetting
 */
class PosSendEmailSetting extends BaseModel
{
    protected $table = 'pos_send_email_setting';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'ps_id',
        'ps_place_id',
        'ps_tempmail_id',
        'ps_special_id',
        'ps_list_giftcustomer_id',
        'ps_list_worker_id',
        'ps_customer_type',
        'ps_customer_time',
        'ps_before_date',
        'ps_default_template',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'ps_status'
    ];

    protected $guarded = [];

        
}