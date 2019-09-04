<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MainUser extends Model
{
	protected $table = 'main_user';

	public $timestamps = true;

	protected $primaryKey = 'user_id';
		
	protected $fillable = [
		'user_id',
		'user_nickname',
		'user_firstname',
		'user_lastname',
		'user_phone',
		'user_country_code',
		'user_password',
		'user_email',
		'user_group_id',
		'user_avatar',
		'user_status',
		'user_token',
	];

	protected $guarded = [];

	public function getFullname(){
		return $this->user_firstname." ".$this->user_lastname;
	}
}