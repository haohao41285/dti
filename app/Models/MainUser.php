<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MainUser extends Model
{
	protected $table = 'main_user';

	public $timestamps = true;

	protected $primaryKey = 'user_id';
		
}