<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MainMfa
 */
class MainMfa extends Model
{
    protected $table = 'main_mfa';

    protected $primaryKey = 'mfa_id';

	public $timestamps = false;

    protected $fillable = [
        'mfa_place_id',
        'mfa_detail'
    ];

    protected $guarded = [];

        
}