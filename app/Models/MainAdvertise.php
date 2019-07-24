<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainAdvertise extends Model
{
	protected $table = 'main_advertise';

	public $timestamps = true;

	protected $fillable = [
		'ad_id',
		'ad_name',
		'ad_place_id',
		'ad_width',
		'ad_height',
		'ad_position',
		'ad_begin',
		'ad_expire',
		'created_at',
		'updated_at',
		'created_by',
		'updated_by',
		'ad_note',
		'ad_status'
	];
	protected $guarded = [];  
}