<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomerWeb
 */
class PosCustomerWeb extends Model
{
    protected $table = 'pos_customer';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'customer_id',
        'customer_fullname',
        'customer_customertag_id',
        'customer_gender',
        'customer_phone',
        'customer_email',
        'customer_birthdate',
        'customer_place_id',
        'customer_status'
    ];

    protected $guarded = [];

        
}