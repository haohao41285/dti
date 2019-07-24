<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainAddress extends Model
{
    protected $table = 'main_address';

    public $timestamps = true;

    protected $fillable = [
        'address_id',
        'address_name',
        'address_parent_id',
        'address_type',
        'address_description',
        'created_at',
        'updated_at',
        'address_status'
    ];

    protected $guarded = [];

        
}