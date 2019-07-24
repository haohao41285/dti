<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

/**
 * Class PosUsergroup
 */
class PosMerchantPerUserGroup extends Model
{
    protected $table = 'pos_merchant_per_user_group';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'pm_id',
        'ug_id',
        'mpug_place_id'
    ];

    protected $guarded = [];

        
}