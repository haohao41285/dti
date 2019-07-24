<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosService
 */
class PosRole extends BaseModel
{
    protected $table = 'pos_role';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'role_id',
        'role_place_id',
        'role_parent_id',
        'role_name',
        'role_description',
        'role_scope',
        'role_alias',
        'created_at',
        'updated_at',
        'role_status'
    ];

    protected $guarded = [];

        
}