<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainCustomerTemplate extends Model
{
    protected $table = "main_customer_template";
    protected $filable = [
    	'ct_salon_name',
    	'ct_fullname',
        'ct_firstname',
        'ct_lastname',
    	'ct_business_phone',
    	'ct_cell_phone',
    	'ct_email',
    	'ct_address',
    	'ct_website',
    	'ct_note',
    	'created_by',
    	'updated_by'
    ];
}