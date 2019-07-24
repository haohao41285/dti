<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainCustomerFiletype extends Model
{
    protected $table = 'main_customer_filetype';

    public $timestamps = true;

    protected $fillable = [
        'cft_id', 
        'cft_name', 
        'cft_description', 
        'created_at',
        'updated_at',
        'cft_status'
    ];

    protected $guarded = [];

        
}