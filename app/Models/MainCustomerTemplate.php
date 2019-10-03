<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainCustomerTemplate extends Model
{
    protected $table = "main_customer_template";
    protected $fillable = [
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
    public function getFullname(){
        return $this->ct_firstname." ".$this->ct_lastname;
    }
    public function getMainCustomer(){
        return $this->hasOne(MainCustomer::class,'customer_customer_template_id','id');
    }
}
