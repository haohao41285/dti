<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosWorker
 */
class PosTax extends BaseModel
{
	protected $table = 'pos_tax';
	protected $prefix = 'tax_';

	public $timestamps = true;

	public static function boot()
    {
        parent::boot();
    }

	protected $fillable = [
		'tax_id',
		'tax_place_id',
		'tax_worker_id',
		'tax_type_link_owner',
		'tax_type_link_worker',
		'tax_form_link_owner',
		'tax_form_link_worker',
		'tax_place_form',
		
		'created_at',
        'updated_at',
		'tax_status'
	];

	protected $guarded = [
		'tax_id',
		'tax_place_id'
	];

	public function getPrefix()
	{
		return $this->prefix;
	}
		
}