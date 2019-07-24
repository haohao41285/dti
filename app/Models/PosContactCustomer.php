<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class PosContactCustomer extends Model
{
    protected $table = 'pos_contact_customer';

    public $timestamps = false;

    protected $fillable = [
        'cc_id',
        'cc_place_id',
        'cc_fullname',
        'cc_email',
        'cc_phone',
        'cc_subject',
        'cc_content',
        'cc_datetime',
        'cc_reply',
        'cc_status'
    ];

    protected $guarded = [];

        
}