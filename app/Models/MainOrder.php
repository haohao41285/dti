<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainOrder extends Model
{
    protected $table = 'main_order';

    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'order_employee_id',
        'order_note',
        'order_datetime',
        'order_service_id',
        'order_package_id',
        'order_user_id',
        'order_place_id',
        'created_at',
        'updated_at',
        'order_status',
    ];

    protected $guarded = [];

        
}