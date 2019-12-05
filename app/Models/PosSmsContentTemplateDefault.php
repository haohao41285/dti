<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class PosSmsContentTemplateDefault extends Model
{
	protected $table = 'pos_sms_content_template_default';

	public $timestamps = true;

	protected $fillable = [
		'sms_content_template_id',
		'template_title',
		'sms_content_template',
		'created_at',
		'updated_at',
		'status'
	];
	protected $guarded = [];  
}
