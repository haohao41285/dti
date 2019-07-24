<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MainCustomerService
 */
class MainCustomerService extends Model
{
	protected $table = 'main_customer_service';

	public $timestamps = true;

	protected $fillable = [
		'cs_id',
		'cs_place_id',
		'cs_customer_id',
		'cs_service_id',
		'cs_date_expire',
		'cs_type',
		'created_by',
		'created_at',
		'updated_by',
		'updated_at',
		'cs_status'
	];

	protected $guarded = [];

}