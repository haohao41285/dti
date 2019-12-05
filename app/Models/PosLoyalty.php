<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 */
class PosLoyalty extends Model
{	
	protected $table = "pos_loyalty";

	protected $primaryKey = 'loyalty_id';

	public $timestamps = false;

	public $created_by = false;

	public $updated_by = false;

	protected $fillable = [
			'loyalty_id' ,
            'loyalty_place_id' ,
            'loyalty_price_to_point' ,
            'loyalty_service_to_point' ,
			'loyalty_point_to_amount' ,
    		'loyalty_paying_by_cash',
    		'loyalty_return_in_a_month' ,
    		'loyalty_referral_gift_card' ,
    		'loyalty_buying_gift_card' ,
    		'loyalty_new_customer' ,
    		'loyalty_vip_customer',
    		'loyalty_for_normal',
    		'loyalty_for_siver' ,
    		'loyalty_for_golden',
    		'loyalty_for_dimond',
    		'loyalty_vip_point',
	];

	protected $guarded = [];  

}