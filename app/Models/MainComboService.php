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
    	'cs_type', // Service or Combo
    	'cs_assign_to',
        'cs_form_type',
        'cs_combo_service_type' //main_combo_service_type
    ];
    public function getComboServiceType(){
        return $this->belongsTo(MainComboServiceType::class,'cs_combo_service_type','id');
    }
}
