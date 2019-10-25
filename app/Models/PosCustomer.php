<?php

namespace App\Models;

use App\Models\BaseModel;
use Session;

/**
 * Class PosCustomer
 */
class PosCustomer extends BaseModel
{
    protected $table = 'pos_customer';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'customer_id',
        'customer_place_id',
        'customer_customertag_id',
        'customer_fullname',
        'customer_gender',
        'customer_phone',
        'customer_history',
        'customer_country_code',
        'customer_email',
        'customer_birthdate',
        'customer_address',
        'customer_description',
        'customer_place_id',
        'customer_point',
        'customer_point_use',
        'customer_point_total',
        'customer_status'
    ];

    protected $guarded = [];

    public function customertag()
    {
        //return $this->hasOne('App\Models\PosCustomertag', 'customertag_id', 'customer_customertag_id');
        return $this->belongsTo('App\Models\PosCustomertag');
    }

    
}