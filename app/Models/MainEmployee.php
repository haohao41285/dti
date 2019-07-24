<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainEmployee extends Model
{
    protected $table = 'main_employee';

    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'employee_firstname',
        'employee_lastname',
        'employee_avatar',
        'employee_birthday',
        'employee_phone',
        'employee_email',
        'employee_description',
        'employee_address',
        'created_at',
        'updated_at',
        'employee_status'
    ];

    protected $guarded = [];

        
}