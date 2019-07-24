<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainServiceDetail extends Model
{
    protected $table = 'main_servicedetail';

    public $timestamps = true;

    protected $fillable = [
        'servicedetail_id',
        'servicedetail_name',
        'servicedetail_price',
        'servicedetail_description',
        'servicedetail_slogan',
        'created_at',
        'updated_at',
        'servicedetail_status'
    ];

    protected $guarded = [];

        
}