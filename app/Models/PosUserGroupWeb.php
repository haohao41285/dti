<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PosUsergroup
 */
class PosUserGroupWeb extends Model
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
        'created_by',
        'updated_at',
        'updated_by',
        'ug_status'
    ];

    protected $guarded = [];

        
}