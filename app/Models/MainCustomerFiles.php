<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MainCustomerFiles
 */
class MainCustomerFiles extends Model
{
	protected $table = 'main_customer_files';

	public $timestamps = true;

	protected $fillable = [
		'bmf_id',
		'bmf_name',
		'bmf_customer_id',
		'bmf_link',
		'bmf_type',
		'created_at',
		'created_by',
		'bmf_status'
	];

	protected $guarded = [];

		
}