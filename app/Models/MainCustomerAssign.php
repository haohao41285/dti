<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainCustomerAssign extends Model
{
    protected $table = 'main_customer_assigns';
    protected $fillable = [
        'user_id',
        'business_phone',
        'business_name',
        'customer_id',
    ];
}
