<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPackage
 */
class PosPackageDetail extends BaseModel
{
	protected $table = 'pos_package_detail';

	public $timestamps = true;

	public static function boot()
	{
		parent::boot();
	}

	protected $fillable = [
		'package_id',
		'packagedetail_id',
		'packagedetail_place_id',
		'packagedetail_package_id',
		'packagedetail_name',
		'packagedetail_price',
		'packagedetail_price_hold',
		'packagedetail_image',
		'packagedetail_description',
		'created_at',
		'updated_at',
		'packagedetail_status'
	];

	protected $guarded = [];

		
}