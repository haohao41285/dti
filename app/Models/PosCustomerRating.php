<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosCustomerRating
 */
class PosCustomerRating extends BaseModel
{
    protected $table = 'pos_customer_rating';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'cr_id',
        'cr_place_id',
        'cr_fullname',
        'cr_phone',
        'cr_rating',
        'cr_email',
        'cr_description',
        'cr_reply',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'cr_status',
        'cr_datetime'
    ];

    protected $guarded = [];

        
}