<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class PosMarketing extends Model{
    protected $table = 'pos_marketing';

    public $timestamps = true;

    protected $fillable = [
        'marketing_id',
        'marketing_place_id',
        'marketing_customer_id',
        'marketing_datetime_send',
        'marketing_content',
        'marketing_status'
    ];

    protected $guarded = [];

        
}