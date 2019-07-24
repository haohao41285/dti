<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosWorker
 */
class PosWorker extends BaseModel
{
	protected $table = 'pos_worker';
	protected $prefix = 'worker_';


	public $timestamps = true;

	public static function boot()
    {
        parent::boot();
    }

	protected $fillable = [
		'worker_id',
		'worker_user_id',
		'worker_code',
		'worker_firstname',
		'worker_lastname',
		'worker_nickname',
		'worker_avatar',
		'worker_birthday',
		'worker_gender',
		'worker_phone',
		'worker_email',
		'worker_address',
		'worker_ssn',
		'worker_zipcode',
		'worker_taxcode',
		'worker_rate',
		'worker_time_type',
		'worker_tax_type',
		'worker_signature',
		'worker_married',
		'worker_family_people',
		'worker_description',
		'worker_date_join',
		'worker_place_id',
		'worker_percent',
		'worker_cash_percent',
		'worker_cash_tax',
		'worker_tip_tax',
		'worker_hour_rate',
		'worker_money_hour_rate',
		'worker_rent_boot',
		'worker_fix_amount',
		'worker_social_security',
		'worker_medicare',
		'worker_sdi',
		'worker_city',
		'worker_state',
		'created_at',
        'updated_at',
		'worker_status'
	];

	protected $guarded = [
		'worker_id',
		'worker_place_id'
	];

	public function getPrefix()
	{
		return $this->prefix;
	}
		
}