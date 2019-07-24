<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainCustomer extends Model
{
    protected $table = 'main_customer';

    public $timestamps = false;

    protected $fillable = [
        'customer_id', 
        'customer_lastname', 
        'customer_firstname', 
        'customer_email', 
        'customer_phone', 
        'customer_phone_introduce', 
        'customer_address',
        'customer_city',
        'customer_zip',
        'customer_state',
        'customer_agent', 
        'customer_type', 
        'customer_status'
    ];

    protected $guarded = [];

        
}