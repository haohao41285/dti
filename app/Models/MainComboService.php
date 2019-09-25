<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainComboService extends Model
{
    protected $table = "main_combo_service";
    protected $fillable = [
    	'cs_name',
    	'cs_price',
    	'cs_expiry_period',
    	'cs_service_id',
    	'cs_menu_id',
    	'cs_description',
    	'cs_status',
    	'cs_type',
    	'cs_assign_to',
    	'routing_number',
    	'account_number',
    	'bank_name'
    ];
}
