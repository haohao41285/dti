<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosUsergroup
 */
class PosUserGroup extends BaseModel
{
    protected $table = 'pos_user_group';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'ug_id',
        'ug_place_id',
        'ug_name',
        'ug_description',
        'ug_role',
        'created_at',
        'updated_at',
        'ug_status'
    ];

    protected $guarded = [];

        
}