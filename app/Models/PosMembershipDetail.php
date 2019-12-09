<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosMembershipDetail extends Model
{
    protected $table = 'pos_membership_detail';

	public $timestamps = true;

	public $primaryKey = 'membership_detail_id';

	protected $fillable = [
        'membership_detail_id',
        'membership_detail_place_id',       
        'membership_detail_listservice',       
        'membership_detail_price',       
        'membership_detail_percent_discount',       
        'membership_detail_membership_id',       
        'membership_detail_time',       
        'membership_detail_status',       
        'created_at',
        'updated_at',     
    ];

    protected $guarded = [];
        
}