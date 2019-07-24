<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosUserWorker
 */
class PosUserWorker extends BaseModel
{
	protected $table = 'pos_user_worker';
	protected $prefix = 'uw_';

	public $timestamps = true;

	public static function boot()
	{
		parent::boot();
	}

	protected $fillable = [
		'worker_id',
		'uw_id',
		'uw_phone',
		'uw_password',
		'uw_nickname',
		'uw_avatar',
		'uw_gender',
		'uw_firstname',
		'uw_lastname',
		'uw_status'
	];

	protected $guarded = [];
}