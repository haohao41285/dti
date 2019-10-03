<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class PosCustomer
 */
class MainCustomer extends Model
{
    use PresentableTrait;
    protected  $presenter = 'App\\Presenters\\MainCustomerPresenter';
    protected $table = 'main_customer';

    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'customer_lastname',
        'customer_firstname',
        'customer_email',
        'customer_phone',
        'customer_phone_introduce',
        'customer_address',
        'customer_city',
        'customer_zip',
        'customer_state',
        'customer_agent',
        'customer_type',
        'customer_status',
        'customer_customer_template_id'
    ];

    protected $guarded = [];

    public function getPlaces(){
        return $this->hasMany(PosPlace::class,'place_customer_id','customer_id');
    }
    public function getOrder(){
        return $this->hasMany(MainComboServiceBought::class,'csb_customer_id','customer_id');
    }
}
