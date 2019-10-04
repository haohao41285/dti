<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class MainComboServiceBought extends Model
{
    use PresentableTrait;
    protected $presenter = 'App\\Presenters\\ThemeMailPresenter';

    protected $table = "main_combo_service_bought";
    protected $fillable = [
    	'csb_customer_id',
        'csb_place_id',
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
        'routing_number',
    ];
    public function getCustomer(){
        return $this->belongsTo(MainCustomer::class,'csb_customer_id','customer_id');
    }
    public function getPlace(){
        return $this->belongsTo(PosPlace::class,'csb_place_id','place_id');
    }
    public function getCreatedBy(){
        return $this->belongsTo(MainUser::class,'created_by','user_id');
    }
    public function getTasks(){
        return $this->hasMany(MainTask::class,'order_id','id');
    }

}
