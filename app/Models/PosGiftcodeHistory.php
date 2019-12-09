<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosGiftcodeHistory extends Model
{
    protected $table = "pos_giftcode_history";
    protected $fillable = [
    	'id',
    	'place_id',
    	'giftcode_code',
    	'giftcode_balance',
    	'giftcode_use',
    	'created_by',
    	'updated_by',
    	'created_at',
    	'updated_at',
    	'giftcode_type',
    	'giftcode_bonus_point',
    	'gitcode_redemption',
    ];
}
