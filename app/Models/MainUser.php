<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainUser extends Model
{
	protected $table = 'pos_user';

	public $timestamps = true;

	protected $fillable = [
		'user_id',
		'user_place_id',
		'user_usergroup_id',
		'user_main_customer_id',
		'user_permission',
		'user_nickname',
		'user_phone',
		'user_email',
		'user_password',
		'user_fullname',
		'user_avatar',
		'user_status',
		'user_token',
		'created_at',
		'updated_at',
		'created_by',
		'updated_by',
		'user_login_time'
	];

	protected $guarded = [];

		
}