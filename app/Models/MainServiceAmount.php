<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainServiceAmount extends Model
{
    protected $table = 'main_service_amount';

    public $timestamps = true;

    protected $fillable = [
        'sa_id',
        'sa_service_id',
        'sa_package_id',
        'sa_price',
        'sa_duration',
        'created_at',
        'updated_at',
        'sa_status'
    ];

    protected $guarded = [];

        
}