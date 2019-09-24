<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainComboServiceBought extends Model
{
    protected $table = "main_combo_service_bought";
    protected $fillable = [
    	'csb_customer_id',
    	'csb_combo_service_id',
        'csb_trans_id',
    	'csb_amount',
    	'csb_charge',
    	'csb_cashback',
    	'csb_payment_method',
    	'csb_card_type',
    	'csb_amount_deal',
    	'csb_card_number',
    	'csb_status',
        'csb_note',
        'created_by',
        'updated_by',
        'bank_name',
        'account_number',
        'routing_number'
    ];
}
