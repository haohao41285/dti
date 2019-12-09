<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosMembership
 */
class PosMembership extends Model
{
    protected $table = 'pos_membership';

	public $timestamps = false;

    protected $fillable = [
        'membership_id',
        'membership_name',       
    ];

    protected $guarded = [];
        
}