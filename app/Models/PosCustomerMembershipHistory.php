<?php

namespace App\Models;

use App\Models\BaseModel;

class PosCustomerMembershipHistory extends BaseModel
{
    public static function boot()
    {
        parent::boot();
    }
    protected $table="pos_customer_membership_history";
    
    protected $fillable = [
    	'cm_place_id',
    	'cm_membership_id',
    	'cm_customer_id',
    	'cm_time_buy',
    	'cm_payment_method',
    	'created_by',
    	'updated_by'
    ];
}
