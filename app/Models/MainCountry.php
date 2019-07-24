<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainCountry extends Model
{
    protected $table = 'main_country';

    public $timestamps = true;

    protected $fillable = [
        'country_id',
        'country_name',
        'country_parent_id',
        'country_code',
        'country_description',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'country_status'
    ];

    protected $guarded = [];

        
}